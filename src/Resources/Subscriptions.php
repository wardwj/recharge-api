<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Subscription;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\SubscriptionSort;
use Recharge\RechargeClient;
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
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return SubscriptionSort::class;
    }

    /**
     * List all subscriptions
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports cursor-based pagination with limit, status, and customer_id filters.
     * Supports sorting via sort_by parameter (SubscriptionSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, status, customer_id, cursor, sort_by, etc.)
     *                                           sort_by can be a SubscriptionSort enum or a string value
     * @return Paginator<Subscription> Paginator instance for iterating subscriptions
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#list-subscriptions
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

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
     * @param array<string, mixed> $data Subscription creation data
     * @return Subscription Created Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#create-a-subscription
     */
    public function create(array $data): Subscription
    {
        $response = $this->client->post($this->endpoint, $data);

        return Subscription::fromArray($response['subscription'] ?? []);
    }

    /**
     * Update an existing subscription
     *
     * @param int $id Subscription ID
     * @param array<string, mixed> $data Subscription update data
     * @return Subscription Updated Subscription DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/subscriptions#update-a-subscription
     */
    public function update(int $id, array $data): Subscription
    {
        $response = $this->client->put($this->buildEndpoint((string) $id), $data);

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

    /**
     * Get count of subscriptions
     *
     * Count endpoint is only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering (status, customer_id, etc.)
     * @return int Count of subscriptions matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/subscriptions#count-subscriptions
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
     * Bulk create subscriptions for an address
     *
     * Creates multiple subscriptions for a specific address in a single request.
     * Only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param int $addressId Address ID
     * @param array<int, array<string, mixed>> $subscriptions Array of subscription data arrays to create
     * @return array<string, mixed> Response data containing created subscriptions
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/subscriptions#bulk-create-subscriptions
     */
    public function bulkCreate(int $addressId, array $subscriptions): array
    {
        $context = $this->switchToVersion(ApiVersion::V2021_01);

        try {
            // Endpoint is /addresses/{id}/subscriptions-bulk, not /subscriptions/...
            $endpoint = "/addresses/{$addressId}/subscriptions-bulk";

            return $this->client->post($endpoint, ['subscriptions' => $subscriptions]);
        } finally {
            $context->restore();
        }
    }

    /**
     * Bulk update subscriptions for an address
     *
     * Updates multiple subscriptions for a specific address in a single request.
     * Only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param int $addressId Address ID
     * @param array<int, array<string, mixed>> $subscriptions Array of subscription data arrays to update (must include id)
     * @return array<string, mixed> Response data containing updated subscriptions
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/subscriptions#bulk-update-subscriptions
     */
    public function bulkUpdate(int $addressId, array $subscriptions): array
    {
        $context = $this->switchToVersion(ApiVersion::V2021_01);

        try {
            // Endpoint is /addresses/{id}/subscriptions-bulk, not /subscriptions/...
            $endpoint = "/addresses/{$addressId}/subscriptions-bulk";

            return $this->client->put($endpoint, ['subscriptions' => $subscriptions]);
        } finally {
            $context->restore();
        }
    }

    /**
     * Bulk delete subscriptions for an address
     *
     * Deletes multiple subscriptions for a specific address in a single request.
     * Only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param int $addressId Address ID
     * @param array<int> $subscriptionIds Array of subscription IDs to delete
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/subscriptions#bulk-delete-subscriptions
     */
    public function bulkDelete(int $addressId, array $subscriptionIds): void
    {
        $context = $this->switchToVersion(ApiVersion::V2021_01);

        try {
            // Endpoint is /addresses/{id}/subscriptions-bulk, not /subscriptions/...
            // Bulk delete uses DELETE with subscription_ids as query parameter
            $subscriptionIdsQuery = http_build_query(['subscription_ids' => implode(',', array_map('strval', $subscriptionIds))]);
            $endpoint = "/addresses/{$addressId}/subscriptions-bulk?{$subscriptionIdsQuery}";

            $this->client->delete($endpoint);
        } finally {
            $context->restore();
        }
    }
}
