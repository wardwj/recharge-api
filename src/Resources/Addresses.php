<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Address;
use Recharge\Enums\ApiVersion;
use Recharge\Support\Paginator;

/**
 * Addresses resource for interacting with Recharge address endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete customer addresses.
 * Addresses are used for shipping subscriptions and can be associated with multiple
 * subscriptions for a single customer.
 *
 * @see https://developer.rechargepayments.com/2021-11/addresses
 */
class Addresses extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/addresses';

    /**
     * List all addresses with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by customer_id, created_at, updated_at, and more.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, customer_id, created_at_min, etc.)
     * @return Paginator<Address> Paginator instance for iterating addresses
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#list-addresses
     */
    public function list(array $queryParams = []): Paginator
    {
        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Address => Address::fromArray($data),
            itemsKey: 'addresses'
        );
    }

    /**
     * Retrieve a specific address by ID
     *
     * @param int $addressId Address ID
     * @return Address Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#retrieve-an-address
     */
    public function get(int $addressId): Address
    {
        $response = $this->client->get($this->buildEndpoint((string) $addressId));

        return Address::fromArray($response['address'] ?? []);
    }

    /**
     * Create a new address
     *
     * @param array<string, mixed> $data Address data (customer_id, address1, city, province, zip, country, etc.)
     * @return Address Created Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#create-an-address
     */
    public function create(array $data): Address
    {
        $response = $this->client->post($this->endpoint, $data);

        return Address::fromArray($response['address'] ?? []);
    }

    /**
     * Update an existing address
     *
     * @param int $addressId Address ID
     * @param array<string, mixed> $data Address data to update (address1, address2, city, zip, etc.)
     * @return Address Updated Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#update-an-address
     */
    public function update(int $addressId, array $data): Address
    {
        $response = $this->client->put($this->buildEndpoint((string) $addressId), $data);

        return Address::fromArray($response['address'] ?? []);
    }

    /**
     * Delete an address
     *
     * Permanently deletes an address. This action cannot be undone.
     * All subscriptions associated with this address must be moved to a different
     * address or cancelled before deletion.
     *
     * @param int $addressId Address ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#delete-an-address
     */
    public function delete(int $addressId): void
    {
        $this->client->delete($this->buildEndpoint((string) $addressId));
    }

    /**
     * Get count of addresses
     *
     * Count endpoint is only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering (customer_id, created_at_min, etc.)
     * @return int Count of addresses matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/addresses#count-addresses
     */
    public function count(array $queryParams = []): int
    {
        $context = $this->switchToVersion(ApiVersion::V2021_01);

        try {
            $response = $this->client->get($this->buildEndpoint('count'), $queryParams);

            return (int) ($response['count'] ?? 0);
        } finally {
            $context->restore();
        }
    }

    /**
     * Validate an address
     *
     * Validates an address before creating it. Only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $data Address data to validate (address1, city, province, zip, country, etc.)
     * @return array<string, mixed> Validation response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/addresses#validate-an-address
     */
    public function validate(array $data): array
    {
        $context = $this->switchToVersion(ApiVersion::V2021_01);

        try {
            return $this->client->post($this->buildEndpoint('validate'), $data);
        } finally {
            $context->restore();
        }
    }
}
