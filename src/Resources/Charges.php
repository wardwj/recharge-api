<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Charge;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\ChargeSort;
use Recharge\Support\Paginator;

/**
 * Charges resource for interacting with Recharge charge endpoints
 *
 * Provides methods to list, retrieve, and manage charges. Charges represent
 * billing attempts for subscriptions and can be processed, skipped, refunded,
 * and more.
 *
 * Note: Some endpoints (e.g., addFreeGift, removeFreeGift) are only available
 * in API version 2021-11. The methods automatically handle version switching.
 *
 * @see https://developer.rechargepayments.com/2021-11/charges
 * @see https://developer.rechargepayments.com/2021-01/charges
 */
class Charges extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/charges';

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return ChargeSort::class;
    }

    /**
     * List all charges with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by status, customer_id, scheduled_at, and more.
     * Supports sorting via sort_by parameter (ChargeSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, scheduled_at_min, sort_by, etc.)
     *                                           sort_by can be a ChargeSort enum or a string value
     * @return Paginator<Charge> Paginator instance for iterating charges
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/charges#list-charges
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Charge => Charge::fromArray($data),
            itemsKey: 'charges'
        );
    }

    /**
     * Retrieve a specific charge by ID
     *
     * @param int $chargeId Charge ID
     * @return Charge Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#retrieve-a-charge
     */
    public function get(int $chargeId): Charge
    {
        $response = $this->client->get($this->buildEndpoint((string) $chargeId));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Apply a discount to a charge
     *
     * Applies a discount code to a charge before it's processed.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Discount data (discount_code, etc.)
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#apply-a-discount
     */
    public function applyDiscount(int $chargeId, array $data): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/apply_discount"), $data);

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Remove a discount from a charge
     *
     * Removes the currently applied discount from a charge.
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#remove-a-discount
     */
    public function removeDiscount(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/remove_discount"));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Skip a charge
     *
     * Skips a charge that is scheduled for processing.
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#skip-a-charge
     */
    public function skip(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/skip"));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Unskip a charge
     *
     * Unskips a charge that was previously skipped.
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#unskip-a-charge
     */
    public function unskip(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/unskip"));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Refund a charge
     *
     * Creates a refund for a previously processed charge.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Refund data (amount, reason, etc.)
     * @return Charge Refunded Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#refund-a-charge
     */
    public function refund(int $chargeId, array $data = []): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/refund"), $data);

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Process a charge
     *
     * Manually processes a charge that is queued or scheduled.
     *
     * @param int $chargeId Charge ID
     * @return Charge Processed Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#process-a-charge
     */
    public function process(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/process"));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Capture a charge
     *
     * Captures an authorized charge. This endpoint requires Pro merchant access.
     *
     * @param int $chargeId Charge ID
     * @return Charge Captured Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#capture-a-charge
     * @see https://developer.rechargepayments.com/2021-01/charges#capture-payment
     */
    public function capture(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/capture_payment"));

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Change next charge date
     *
     * Updates the scheduled date for a charge.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Charge date data (scheduled_at, etc.)
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#change-next-charge-date
     * @see https://developer.rechargepayments.com/2021-01/charges#change-next-charge-date
     */
    public function changeNextChargeDate(int $chargeId, array $data): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/change_next_charge_date"), $data);

        return Charge::fromArray($response['charge'] ?? []);
    }

    /**
     * Add free gift to a charge
     *
     * Adds a free gift variant to a charge. Only available in API version 2021-11.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Free gift data (external_variant_id, etc.)
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#add-free-gift
     */
    public function addFreeGift(int $chargeId, array $data): Charge
    {
        // Ensure we're using 2021-11 API version for this endpoint
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            $response = $this->client->post($this->buildEndpoint("{$chargeId}/add_free_gift"), $data);

            return Charge::fromArray($response['charge'] ?? []);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Remove free gift from a charge
     *
     * Removes a free gift variant from a charge. Only available in API version 2021-11.
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Free gift data (external_variant_id, etc.)
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#remove-free-gift
     */
    public function removeFreeGift(int $chargeId, array $data): Charge
    {
        // Ensure we're using 2021-11 API version for this endpoint
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            $response = $this->client->post($this->buildEndpoint("{$chargeId}/remove_free_gift"), $data);

            return Charge::fromArray($response['charge'] ?? []);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Get count of charges
     *
     * Count endpoint is only available in API version 2021-01.
     * This method temporarily switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering (status, customer_id, scheduled_at_min, etc.)
     * @return int Count of charges matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/charges#count-charges
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
}
