<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Discount;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\DiscountSort;
use Recharge\Support\Paginator;

/**
 * Discounts resource for interacting with Recharge discount endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete discounts,
 * as well as apply/remove discounts to addresses and charges.
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts
 * @see https://developer.rechargepayments.com/2021-01/discounts
 */
class Discounts extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/discounts';

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return DiscountSort::class;
    }

    /**
     * List all discounts with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by status, code, created_at, updated_at, and more.
     * Supports sorting via sort_by parameter (DiscountSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, code, created_at_min, sort_by, etc.)
     *                                           sort_by can be a DiscountSort enum or a string value
     * @return Paginator<Discount> Paginator instance for iterating discounts
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/discounts#list-discounts
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Discount => Discount::fromArray($data),
            itemsKey: 'discounts'
        );
    }

    /**
     * Retrieve a specific discount by ID
     *
     * @param int $discountId Discount ID
     * @return Discount Discount DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#retrieve-a-discount
     */
    public function get(int $discountId): Discount
    {
        $response = $this->client->get($this->buildEndpoint((string) $discountId));

        return Discount::fromArray($response['discount'] ?? []);
    }

    /**
     * Create a new discount
     *
     * @param array<string, mixed> $data Discount data (code, discount_type/value_type, value, duration, etc.)
     * @return Discount Created Discount DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#create-a-discount
     */
    public function create(array $data): Discount
    {
        $response = $this->client->post($this->endpoint, $data);

        return Discount::fromArray($response['discount'] ?? []);
    }

    /**
     * Update an existing discount
     *
     * @param int $discountId Discount ID
     * @param array<string, mixed> $data Discount data to update
     * @return Discount Updated Discount DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#update-a-discount
     */
    public function update(int $discountId, array $data): Discount
    {
        $response = $this->client->put($this->buildEndpoint((string) $discountId), $data);

        return Discount::fromArray($response['discount'] ?? []);
    }

    /**
     * Delete a discount
     *
     * Permanently deletes a discount. This action cannot be undone.
     *
     * @param int $discountId Discount ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#delete-a-discount
     */
    public function delete(int $discountId): void
    {
        $this->client->delete($this->buildEndpoint((string) $discountId));
    }

    /**
     * Get count of discounts
     *
     * Count endpoint is only available in API version 2021-01.
     * This method temporarily switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering (status, code, etc.)
     * @return int Count of discounts matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/discounts#count-discounts
     */
    public function count(array $queryParams = []): int
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Count endpoint requires 2021-01 API version
            $this->client->setApiVersion(ApiVersion::V2021_01);
            $response = $this->client->get($this->buildEndpoint('count'), $queryParams);

            return (int) ($response['count'] ?? 0);
        } finally {
            // Restore original API version
            $this->client->setApiVersion($originalVersion);
        }
    }

    /**
     * Apply a discount to an address
     *
     * Applies a discount code to a specific address.
     *
     * @param int $addressId Address ID
     * @param array<string, mixed> $data Discount data (discount_code)
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#apply-a-discount-to-an-address
     */
    public function applyToAddress(int $addressId, array $data): array
    {
        return $this->client->post(
            $this->buildEndpoint('apply_to_address'),
            array_merge($data, ['address_id' => $addressId])
        );
    }

    /**
     * Apply a discount to a charge
     *
     * Applies a discount code to a queued charge.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Discount data (discount_code)
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#apply-a-discount-to-a-charge
     */
    public function applyToCharge(int $chargeId, array $data): array
    {
        return $this->client->post(
            $this->buildEndpoint('apply_to_charge'),
            array_merge($data, ['charge_id' => $chargeId])
        );
    }

    /**
     * Remove a discount
     *
     * Removes a discount from an address or charge.
     *
     * @param array<string, mixed> $data Data containing address_id or charge_id
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/discounts#remove-a-discount
     */
    public function remove(array $data): array
    {
        return $this->client->post($this->buildEndpoint('remove'), $data);
    }
}
