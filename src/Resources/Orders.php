<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Orders resource for interacting with Recharge order endpoints
 *
 * @package Recharge\Resources
 * @see https://developer.rechargepayments.com/2021-11/orders
 */
class Orders extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/orders';

    /**
     * Orders constructor
     *
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all orders
     *
     * @param array<string, mixed> $query Query parameters (limit, page, created_at_min, etc.)
     * @return array<int, Order> Array of Order DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#list-orders
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $orders = $response['orders'] ?? [];

        return array_map(function (array $orderData) {
            return DTOFactory::createOrder($this->client, $orderData);
        }, $orders);
    }

    /**
     * Retrieve a specific order by ID
     *
     * @param int $orderId Order ID
     * @return Order Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#retrieve-an-order
     */
    public function get(int $orderId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$orderId));
        return DTOFactory::createOrder($this->client, $response['order'] ?? []);
    }

    /**
     * Update an existing order
     *
     * @param int $orderId Order ID
     * @param array<string, mixed> $data Order data to update
     * @return Order Updated Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#update-an-order
     */
    public function update(int $orderId, array $data): object
    {
        $response = $this->client->put($this->buildEndpoint((string)$orderId), $data);
        return DTOFactory::createOrder($this->client, $response['order'] ?? []);
    }

    /**
     * Delete an order
     *
     * @param int $orderId Order ID
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#delete-an-order
     */
    public function delete(int $orderId): void
    {
        $this->client->delete($this->buildEndpoint((string)$orderId));
    }

    /**
     * Clone an order
     *
     * @param int $orderId Order ID to clone
     * @param array<string, mixed> $data Clone data
     * @return Order Cloned Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#clone-an-order
     */
    public function clone(int $orderId, array $data = []): object
    {
        $response = $this->client->post($this->buildEndpoint("{$orderId}/clone"), $data);
        return DTOFactory::createOrder($this->client, $response['order'] ?? []);
    }

    /**
     * Delay an order
     *
     * @param int $orderId Order ID
     * @param array<string, mixed> $data Delay data (delay_charge_date, etc.)
     * @return Order Delayed Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#delay-an-order
     */
    public function delay(int $orderId, array $data): object
    {
        $response = $this->client->post($this->buildEndpoint("{$orderId}/delay"), $data);
        return DTOFactory::createOrder($this->client, $response['order'] ?? []);
    }
}
