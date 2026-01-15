<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Charges resource for interacting with Recharge charge endpoints
 *
 * @package Recharge\Resources
 * @see https://developer.rechargepayments.com/2021-11/charges
 */
class Charges extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/charges';

    /**
     * Charges constructor
     *
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all charges
     *
     * @param array<string, mixed> $query Query parameters (limit, page, scheduled_at_min, etc.)
     * @return array<int, Charge> Array of Charge DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#list-charges
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $charges = $response['charges'] ?? [];

        return array_map(function (array $chargeData) {
            return DTOFactory::createCharge($this->client, $chargeData);
        }, $charges);
    }

    /**
     * Retrieve a specific charge by ID
     *
     * @param int $chargeId Charge ID
     * @return Charge Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#retrieve-a-charge
     */
    public function get(int $chargeId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$chargeId));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Apply a discount to a charge
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Discount data (discount_code, etc.)
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#apply-a-discount
     */
    public function applyDiscount(int $chargeId, array $data): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/apply_discount"), $data);
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Remove a discount from a charge
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#remove-a-discount
     */
    public function removeDiscount(int $chargeId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/remove_discount"));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Skip a charge
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#skip-a-charge
     */
    public function skip(int $chargeId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/skip"));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Unskip a charge
     *
     * @param int $chargeId Charge ID
     * @return Charge Updated Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#unskip-a-charge
     */
    public function unskip(int $chargeId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/unskip"));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Refund a charge
     *
     * @param int $chargeId Charge ID
     * @param array<string, mixed> $data Refund data (amount, reason, etc.)
     * @return Charge Refunded Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#refund-a-charge
     */
    public function refund(int $chargeId, array $data = []): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/refund"), $data);
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Process a charge
     *
     * @param int $chargeId Charge ID
     * @return Charge Processed Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#process-a-charge
     */
    public function process(int $chargeId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/process"));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }

    /**
     * Capture a charge
     *
     * @param int $chargeId Charge ID
     * @return Charge Captured Charge DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/charges#capture-a-charge
     */
    public function capture(int $chargeId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$chargeId}/capture"));
        return DTOFactory::createCharge($this->client, $response['charge'] ?? []);
    }
}
