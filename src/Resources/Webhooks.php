<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Webhook;
use Recharge\Enums\Sort\WebhookSort;
use Recharge\Support\Paginator;

/**
 * Webhooks resource for interacting with Recharge webhook endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete webhooks.
 * Webhooks allow you to subscribe to Recharge events and receive notifications
 * when those events occur.
 *
 * @see https://developer.rechargepayments.com/2021-11/webhooks
 * @see https://developer.rechargepayments.com/2021-01/webhooks
 */
class Webhooks extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/webhooks';

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return WebhookSort::class;
    }

    /**
     * List all webhooks with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports sorting via sort_by parameter (WebhookSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, sort_by, cursor, etc.)
     *                                           sort_by can be a WebhookSort enum or a string value
     * @return Paginator<Webhook> Paginator instance for iterating webhooks
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/webhooks#list-webhooks
     * @see https://developer.rechargepayments.com/2021-01/webhooks#list-webhooks
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Webhook => Webhook::fromArray($data),
            itemsKey: 'webhooks'
        );
    }

    /**
     * Retrieve a specific webhook by ID
     *
     * @param int $webhookId Webhook ID
     * @return Webhook Webhook DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/webhooks#retrieve-a-webhook
     * @see https://developer.rechargepayments.com/2021-01/webhooks#retrieve-a-webhook
     */
    public function get(int $webhookId): Webhook
    {
        $response = $this->client->get($this->buildEndpoint((string) $webhookId));

        return Webhook::fromArray($response['webhook'] ?? []);
    }

    /**
     * Create a new webhook
     *
     * @param array<string, mixed> $data Webhook data (address, topics)
     *                                   address: Webhook URL (required)
     *                                   topics: Array of webhook topics/events (required)
     * @return Webhook Created Webhook DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/webhooks#create-a-webhook
     * @see https://developer.rechargepayments.com/2021-01/webhooks#create-a-webhook
     */
    public function create(array $data): Webhook
    {
        $response = $this->client->post($this->endpoint, $data);

        return Webhook::fromArray($response['webhook'] ?? []);
    }

    /**
     * Update an existing webhook
     *
     * @param int $webhookId Webhook ID
     * @param array<string, mixed> $data Webhook data to update (address, topics)
     * @return Webhook Updated Webhook DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/webhooks#update-a-webhook
     * @see https://developer.rechargepayments.com/2021-01/webhooks#update-a-webhook
     */
    public function update(int $webhookId, array $data): Webhook
    {
        $response = $this->client->put($this->buildEndpoint((string) $webhookId), $data);

        return Webhook::fromArray($response['webhook'] ?? []);
    }

    /**
     * Delete a webhook
     *
     * @param int $webhookId Webhook ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/webhooks#delete-a-webhook
     * @see https://developer.rechargepayments.com/2021-01/webhooks#delete-a-webhook
     */
    public function delete(int $webhookId): void
    {
        $this->client->delete($this->buildEndpoint((string) $webhookId));
    }

    /**
     * Test a webhook
     *
     * Sends a test webhook event to the webhook URL to verify it's working correctly.
     *
     * @param int $webhookId Webhook ID
     * @return array<string, mixed> Test response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/webhooks#test-a-webhook
     * @see https://developer.rechargepayments.com/2021-01/webhooks#test-a-webhook
     */
    public function test(int $webhookId): array
    {
        return $this->client->post($this->buildEndpoint("{$webhookId}/test"));
    }
}
