<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Subscriptions resource for interacting with Recharge subscription endpoints
 *
 * @package Recharge\Resources
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
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all subscriptions
     *
     * @param array<string, mixed> $query Query parameters (limit, page, status, etc.)
     * @return array<int, object> Array of Subscription DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#list-subscriptions
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $subscriptions = $response['subscriptions'] ?? [];

        return array_map(function (array $subscriptionData) {
            return DTOFactory::createSubscription($this->client, $subscriptionData);
        }, $subscriptions);
    }

    /**
     * Retrieve a specific subscription by ID
     *
     * @param int $subscriptionId Subscription ID
     * @return object Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#retrieve-a-subscription
     */
    public function get(int $subscriptionId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$subscriptionId));
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Create a new subscription
     *
     * @param array<string, mixed> $data Subscription data
     * @return object Created Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#create-a-subscription
     */
    public function create(array $data): object
    {
        $response = $this->client->post($this->endpoint, $data);
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Update an existing subscription
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Subscription data to update
     * @return object Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#update-a-subscription
     */
    public function update(int $subscriptionId, array $data): object
    {
        $response = $this->client->put($this->buildEndpoint((string)$subscriptionId), $data);
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Delete a subscription
     *
     * @param int $subscriptionId Subscription ID
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#delete-a-subscription
     */
    public function delete(int $subscriptionId): void
    {
        $this->client->delete($this->buildEndpoint((string)$subscriptionId));
    }

    /**
     * Cancel a subscription
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Cancellation data (cancellation_reason, etc.)
     * @return object Cancelled Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#cancel-a-subscription
     */
    public function cancel(int $subscriptionId, array $data = []): object
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/cancel"), $data);
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Activate a subscription
     *
     * @param int $subscriptionId Subscription ID
     * @return object Activated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#activate-a-subscription
     */
    public function activate(int $subscriptionId): object
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/activate"));
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Change a subscription's next charge date
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Date data (next_charge_scheduled_at)
     * @return object Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#change-a-subscription-next-charge-date
     */
    public function changeNextChargeDate(int $subscriptionId, array $data): object
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/set_next_charge_scheduled_at"), $data);
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }

    /**
     * Change a subscription's address
     *
     * @param int $subscriptionId Subscription ID
     * @param array<string, mixed> $data Address data
     * @return object Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#change-a-subscription-address
     */
    public function changeAddress(int $subscriptionId, array $data): object
    {
        $response = $this->client->post($this->buildEndpoint("{$subscriptionId}/change_address"), $data);
        return DTOFactory::createSubscription($this->client, $response['subscription'] ?? []);
    }
}
