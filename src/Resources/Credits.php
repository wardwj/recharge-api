<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Credit;
use Recharge\Support\Paginator;

/**
 * Credits resource for interacting with Recharge credit endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete store credits.
 * Store credits are applied to customer accounts and can be used to offset
 * future charges.
 *
 * @see https://developer.rechargepayments.com/2021-11/credits
 * @see https://developer.rechargepayments.com/2021-01/credits
 */
class Credits extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/credits';

    /**
     * List all credits with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by customer_id, created_at, updated_at, and more.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, customer_id, created_at_min, etc.)
     * @return Paginator<Credit> Paginator instance for iterating credits
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/credits#list-credits
     * @see https://developer.rechargepayments.com/2021-01/credits#list-credits
     */
    public function list(array $queryParams = []): Paginator
    {
        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Credit => Credit::fromArray($data),
            itemsKey: 'credits'
        );
    }

    /**
     * Retrieve a specific credit by ID
     *
     * @param int $creditId Credit ID
     * @return Credit Credit DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/credits#retrieve-a-credit
     * @see https://developer.rechargepayments.com/2021-01/credits#retrieve-a-credit
     */
    public function get(int $creditId): Credit
    {
        $response = $this->client->get($this->buildEndpoint((string) $creditId));

        return Credit::fromArray($response['credit'] ?? []);
    }

    /**
     * Create a new credit
     *
     * @param array<string, mixed> $data Credit data (customer_id, amount, currency, note, etc.)
     * @return Credit Created Credit DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/credits#create-a-credit
     * @see https://developer.rechargepayments.com/2021-01/credits#create-a-credit
     */
    public function create(array $data): Credit
    {
        $response = $this->client->post($this->endpoint, $data);

        return Credit::fromArray($response['credit'] ?? []);
    }

    /**
     * Update an existing credit
     *
     * @param int $creditId Credit ID
     * @param array<string, mixed> $data Credit data to update (amount, note, etc.)
     * @return Credit Updated Credit DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/credits#update-a-credit
     * @see https://developer.rechargepayments.com/2021-01/credits#update-a-credit
     */
    public function update(int $creditId, array $data): Credit
    {
        $response = $this->client->put($this->buildEndpoint((string) $creditId), $data);

        return Credit::fromArray($response['credit'] ?? []);
    }

    /**
     * Delete a credit
     *
     * Permanently deletes a credit. This action cannot be undone.
     *
     * @param int $creditId Credit ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/credits#delete-a-credit
     * @see https://developer.rechargepayments.com/2021-01/credits#delete-a-credit
     */
    public function delete(int $creditId): void
    {
        $this->client->delete($this->buildEndpoint((string) $creditId));
    }
}
