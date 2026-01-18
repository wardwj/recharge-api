<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Checkout;

/**
 * Checkouts resource for interacting with Recharge checkout endpoints
 *
 * Provides methods to create, retrieve, update, and process checkouts.
 * Checkouts are only available for BigCommerce and Custom setups.
 * Not supported for Shopify stores (deprecated as of October 18, 2024).
 *
 * Note: Requires Pro or Custom plan to use.
 *
 * @see https://developer.rechargepayments.com/2021-11/checkouts
 * @see https://developer.rechargepayments.com/2021-01/checkouts
 */
class Checkouts extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/checkouts';

    /**
     * Create a new checkout
     *
     * Initializes a checkout object with items, customer info, etc.
     *
     * @param array<string, mixed> $data Checkout data (line_items, email, billing_address, etc.)
     * @return Checkout Created Checkout DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/checkouts#create-a-checkout
     * @see https://developer.rechargepayments.com/2021-01/checkouts#create-a-checkout
     */
    public function create(array $data): Checkout
    {
        $response = $this->client->post($this->endpoint, $data);

        return Checkout::fromArray($response['checkout'] ?? []);
    }

    /**
     * Retrieve a specific checkout by token
     *
     * @param string $token Checkout token
     * @return Checkout Checkout DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/checkouts#retrieve-a-checkout
     * @see https://developer.rechargepayments.com/2021-01/checkouts#retrieve-a-checkout
     */
    public function get(string $token): Checkout
    {
        $response = $this->client->get($this->buildEndpoint($token));

        return Checkout::fromArray($response['checkout'] ?? []);
    }

    /**
     * Update an existing checkout
     *
     * Updates checkout attributes (e.g., change billing address, add items).
     *
     * @param string $token Checkout token
     * @param array<string, mixed> $data Checkout data to update
     * @return Checkout Updated Checkout DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/checkouts#update-a-checkout
     * @see https://developer.rechargepayments.com/2021-01/checkouts#update-a-checkout
     */
    public function update(string $token, array $data): Checkout
    {
        $response = $this->client->put($this->buildEndpoint($token), $data);

        return Checkout::fromArray($response['checkout'] ?? []);
    }

    /**
     * Get shipping rates for a checkout
     *
     * Lists possible shipping options for this checkout.
     *
     * @param string $token Checkout token
     * @param array<string, mixed> $queryParams Query parameters (optional)
     * @return array<string, mixed> Shipping rates response
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/checkouts#get-shipping-rates
     * @see https://developer.rechargepayments.com/2021-01/checkouts#get-shipping-rates
     */
    public function getShippingRates(string $token, array $queryParams = []): array
    {
        return $this->client->get($this->buildEndpoint("{$token}/shipping_rates"), $queryParams);
    }

    /**
     * Process/charge a checkout
     *
     * Finalizes checkout - processes payment and creates order.
     *
     * @param string $token Checkout token
     * @param array<string, mixed> $data Charge data (payment_method, etc.)
     * @return Checkout Processed Checkout DTO (includes charge_id)
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/checkouts#process-a-checkout
     * @see https://developer.rechargepayments.com/2021-01/checkouts#process-a-checkout
     */
    public function charge(string $token, array $data = []): Checkout
    {
        $response = $this->client->post($this->buildEndpoint("{$token}/charge"), $data);

        return Checkout::fromArray($response['checkout'] ?? []);
    }
}
