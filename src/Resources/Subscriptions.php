<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Subscription;
use Recharge\RechargeClient;
use Recharge\Requests\CreateSubscriptionData;
use Recharge\Requests\UpdateSubscriptionData;
use Recharge\Support\Paginator;

/**
 * Subscriptions resource for interacting with Recharge subscription endpoints
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions
 */
class Subscriptions extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/subscriptions';

    /**
     * Subscriptions constructor
     *
     * @param RechargeClient $client The Recharge API client instance
     */
    public function __construct(RechargeClient $client)
    {
        parent::__construct($client);
    }

    /**
     * List all subscriptions
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports cursor-based pagination with limit, status, and customer_id filters.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, cursor, etc.)
     * @return Paginator<Subscription> Paginator instance for iterating subscriptions
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#list-subscriptions
     */
    public function list(array $queryParams = []): Paginator
    {
        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Subscription => Subscription::fromArray($data),
            itemsKey: 'subscriptions'
        );
    }

    /**
     * Retrieve a specific subscription by ID
     *
     * @param int $id Subscription ID
     * @return Subscription Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#retrieve-a-subscription
     */
    public function get(int $id): Subscription
    {
        $response = $this->client->get($this->buildEndpoint((string) $id));

        return Subscription::fromArray($response['subscription'] ?? []);
    }

    /**
     * Create a new subscription
     *
     * @param CreateSubscriptionData $data Subscription creation data
     * @return Subscription Created Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#create-a-subscription
     */
    public function create(CreateSubscriptionData $data): Subscription
    {
        $response = $this->client->post($this->endpoint, $data->toArray());

        return Subscription::fromArray($response['subscription'] ?? []);
    }

    /**
     * Update an existing subscription
     *
     * @param int $id Subscription ID
     * @param UpdateSubscriptionData $data Subscription update data
     * @return Subscription Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#update-a-subscription
     */
    public function update(int $id, UpdateSubscriptionData $data): Subscription
    {
        $response = $this->client->put($this->buildEndpoint((string) $id), $data->toArray());

        return Subscription::fromArray($response['subscription'] ?? []);
    }

    /**
     * Delete a subscription
     *
     * @param int $id Subscription ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#delete-a-subscription
     */
    public function delete(int $id): void
    {
        $this->client->delete($this->buildEndpoint((string) $id));
    }

    /**
     * Cancel a subscription
     *
     * @param int $id Subscription ID
     * @param string $cancellationReason Reason for cancellation
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#cancel-a-subscription
     */
    public function cancel(int $id, string $cancellationReason): void
    {
        $this->client->post(
            $this->buildEndpoint("{$id}/cancel"),
            ['cancellation_reason' => $cancellationReason]
        );
    }

    /**
     * Activate a subscription
     *
     * @param int $id Subscription ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#activate-a-subscription
     */
    public function activate(int $id): void
    {
        $this->client->post($this->buildEndpoint("{$id}/activate"));
    }

    /**
     * Change a subscription's next charge date
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Date data (next_charge_scheduled_at)
     * @return Subscription Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#change-a-subscription-next-charge-date
     */
    public function changeNextChargeDate(int $subscriptionId, array $data): Subscription
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/set_next_charge_scheduled_at"), $data);

        return Subscription::fromArray($response['subscription'] ?? []);
    }

    /**
     * Change a subscription's address
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Address data
     * @return Subscription Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#change-a-subscription-address
     */
    public function changeAddress(int $subscriptionId, array $data): Subscription
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/change_address"), $data);

        return Subscription::fromArray($response['subscription'] ?? []);
    }
}
