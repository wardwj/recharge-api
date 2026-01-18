<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Metafield;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\MetafieldSort;
use Recharge\Support\Paginator;

/**
 * Metafields resource for interacting with Recharge metafield endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete metafields.
 * Metafields allow you to store custom key-value pairs for various resources
 * such as customers, subscriptions, charges, and more.
 *
 * @see https://developer.rechargepayments.com/2021-11/metafields
 * @see https://developer.rechargepayments.com/2021-01/metafields
 */
class Metafields extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/metafields';

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return MetafieldSort::class;
    }

    /**
     * List all metafields with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by owner_resource, owner_id, namespace, key, and more.
     * Supports sorting via sort_by parameter (MetafieldSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, owner_resource, owner_id, namespace, key, sort_by, etc.)
     *                                           sort_by can be a MetafieldSort enum or a string value
     * @return Paginator<Metafield> Paginator instance for iterating metafields
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/metafields#list-metafields
     * @see https://developer.rechargepayments.com/2021-01/metafields#list-metafields
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Metafield => Metafield::fromArray($data),
            itemsKey: 'metafields'
        );
    }

    /**
     * Retrieve a specific metafield by ID
     *
     * @param int $metafieldId Metafield ID
     * @return Metafield Metafield DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/metafields#retrieve-a-metafield
     * @see https://developer.rechargepayments.com/2021-01/metafields#retrieve-a-metafield
     */
    public function get(int $metafieldId): Metafield
    {
        $response = $this->client->get($this->buildEndpoint((string) $metafieldId));

        return Metafield::fromArray($response['metafield'] ?? []);
    }

    /**
     * Create a new metafield
     *
     * @param array<string, mixed> $data Metafield data (owner_resource, owner_id, namespace, key, value, type, description)
     * @return Metafield Created Metafield DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/metafields#create-a-metafield
     * @see https://developer.rechargepayments.com/2021-01/metafields#create-a-metafield
     */
    public function create(array $data): Metafield
    {
        $response = $this->client->post($this->endpoint, $data);

        return Metafield::fromArray($response['metafield'] ?? []);
    }

    /**
     * Update an existing metafield
     *
     * @param int $metafieldId Metafield ID
     * @param array<string, mixed> $data Metafield data to update (value, type, description, etc.)
     * @return Metafield Updated Metafield DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/metafields#update-a-metafield
     * @see https://developer.rechargepayments.com/2021-01/metafields#update-a-metafield
     */
    public function update(int $metafieldId, array $data): Metafield
    {
        $response = $this->client->put($this->buildEndpoint((string) $metafieldId), $data);

        return Metafield::fromArray($response['metafield'] ?? []);
    }

    /**
     * Delete a metafield
     *
     * Permanently deletes a metafield. This action cannot be undone.
     *
     * @param int $metafieldId Metafield ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/metafields#delete-a-metafield
     * @see https://developer.rechargepayments.com/2021-01/metafields#delete-a-metafield
     */
    public function delete(int $metafieldId): void
    {
        $this->client->delete($this->buildEndpoint((string) $metafieldId));
    }

    /**
     * Get count of metafields
     *
     * Count endpoint is only available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering (owner_resource, owner_id, namespace, key, etc.)
     * @return int Count of metafields matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/metafields#count-metafields
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
}
