<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Order;
use Recharge\Support\Paginator;

/**
 * Orders resource for interacting with Recharge order endpoints
 *
 * Provides methods to list, retrieve, update, and manage orders. Orders are
 * created from charges and represent the fulfillment records sent to your
 * ecommerce platform.
 *
 * @see https://developer.rechargepayments.com/2021-11/orders
 */
class Orders extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/orders';

    /**
     * List all orders with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by status, customer_id, created_at, updated_at, and more.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, created_at_min, etc.)
     * @return Paginator<Order> Paginator instance for iterating orders
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#list-orders
     */
    public function list(array $queryParams = []): Paginator
    {
        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Order => Order::fromArray($data),
            itemsKey: 'orders'
        );
    }

    /**
     * Retrieve a specific order by ID
     *
     * @param int $orderId Order ID
     * @return Order Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#retrieve-an-order
     */
    public function get(int $orderId): Order
    {
        $response = $this->client->get($this->buildEndpoint((string) $orderId));

        return Order::fromArray($response['order'] ?? []);
    }

    /**
     * Update an existing order
     *
     * Updates properties on an order that has not yet been submitted to the
     * ecommerce platform.
     *
     * @param int $orderId Order ID
     * @param array<string, mixed> $data Order data to update (scheduled_at, etc.)
     * @return Order Updated Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#update-an-order
     */
    public function update(int $orderId, array $data): Order
    {
        $response = $this->client->put($this->buildEndpoint((string) $orderId), $data);

        return Order::fromArray($response['order'] ?? []);
    }

    /**
     * Delete an order
     *
     * Permanently deletes an order that has not yet been sent to the ecommerce platform.
     * This action cannot be undone.
     *
     * @param int $orderId Order ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#delete-an-order
     */
    public function delete(int $orderId): void
    {
        $this->client->delete($this->buildEndpoint((string) $orderId));
    }

    /**
     * Clone an order
     *
     * Creates a copy of an existing order with a new scheduled date.
     *
     * @param int $orderId Order ID to clone
     * @param array<string, mixed> $data Clone data (scheduled_at, etc.)
     * @return Order Cloned Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#clone-an-order
     */
    public function clone(int $orderId, array $data = []): Order
    {
        $response = $this->client->post($this->buildEndpoint("{$orderId}/clone"), $data);

        return Order::fromArray($response['order'] ?? []);
    }

    /**
     * Delay an order
     *
     * Delays an order by updating its scheduled date.
     *
     * @param int $orderId Order ID
     * @param array<string, mixed> $data Delay data (scheduled_at, etc.)
     * @return Order Delayed Order DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/orders#delay-an-order
     */
    public function delay(int $orderId, array $data): Order
    {
        $response = $this->client->post($this->buildEndpoint("{$orderId}/delay"), $data);

        return Order::fromArray($response['order'] ?? []);
    }
}
