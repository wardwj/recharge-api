<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Charge;
use Recharge\Support\Paginator;

/**
 * Charges resource for interacting with Recharge charge endpoints
 *
 * Provides methods to list, retrieve, and manage charges. Charges represent
 * billing attempts for subscriptions and can be processed, skipped, refunded,
 * and more.
 *
 * @see https://developer.rechargepayments.com/2021-11/charges
 */
class Charges extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/charges';

    /**
     * List all charges with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by status, customer_id, scheduled_at, and more.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, scheduled_at_min, etc.)
     * @return Paginator<Charge> Paginator instance for iterating charges
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#list-charges
     */
    public function list(array $queryParams = []): Paginator
    {
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
     * Captures an authorized charge.
     *
     * @param int $chargeId Charge ID
     * @return Charge Captured Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#capture-a-charge
     */
    public function capture(int $chargeId): Charge
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/capture"));

        return Charge::fromArray($response['charge'] ?? []);
    }
}
