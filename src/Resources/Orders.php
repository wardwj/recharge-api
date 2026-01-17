<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Order;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\OrderSort;
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
     * Supports sorting via sort_by parameter (OrderSort enum or string).
     *
     * Note: Orders sorting is only available in API version 2021-01.
     * This method automatically switches to 2021-01 when sort_by is provided.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, created_at_min, sort_by, etc.)
     *                                           sort_by can be an OrderSort enum or a string value
     * @return Paginator<Order> Paginator instance for iterating orders
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-01/orders#list-orders
     */
    public function list(array $queryParams = []): Paginator
    {
        $needsVersionSwitch = false;

        // Convert enum to string if provided
        if (isset($queryParams['sort_by']) && $queryParams['sort_by'] instanceof OrderSort) {
            $queryParams['sort_by'] = $queryParams['sort_by']->value;
            $needsVersionSwitch = true;
        }

        // Validate sort_by string if provided
        if (isset($queryParams['sort_by']) && is_string($queryParams['sort_by'])) {
            if (OrderSort::tryFromString($queryParams['sort_by']) === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid sort_by value "%s". Allowed values: %s',
                        $queryParams['sort_by'],
                        implode(', ', array_column(OrderSort::cases(), 'value'))
                    )
                );
            }
            $needsVersionSwitch = true;
        }

        // Orders sorting requires 2021-01 API version
        // Note: We switch the version and keep it switched since Paginator is lazy
        // and makes requests during iteration. The version will remain 2021-01
        // until the user switches it back or creates a new paginator.
        if ($needsVersionSwitch) {
            $this->client->setApiVersion(ApiVersion::V2021_01);
        }

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
