<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Collection;
use Recharge\Enums\ApiVersion;
use Recharge\Support\Paginator;

/**
 * Collections resource for interacting with Recharge collection endpoints
 *
 * Provides methods to list, retrieve, create, and update collections.
 * Collections are used to organize products in your store.
 *
 * Note: Collections are only available in API version 2021-11.
 * All methods automatically switch to 2021-11 if needed, then restore the original version.
 *
 * @see https://developer.rechargepayments.com/2021-11/collections
 */
class Collections extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/collections';

    /**
     * List all collections with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by title.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, title, etc.)
     * @return Paginator<Collection> Paginator instance for iterating collections
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#list-collections
     */
    public function list(array $queryParams = []): Paginator
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            return new Paginator(
                client: $this->client,
                endpoint: $this->endpoint,
                queryParams: $queryParams,
                mapper: fn (array $data): \Recharge\Data\Collection => Collection::fromArray($data),
                itemsKey: 'collections'
            );
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Retrieve a specific collection by ID
     *
     * @param int $collectionId Collection ID
     * @return Collection Collection DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#retrieve-a-collection
     */
    public function get(int $collectionId): Collection
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            $response = $this->client->get($this->buildEndpoint((string) $collectionId));

            return Collection::fromArray($response['collection'] ?? []);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Create a new collection
     *
     * @param array<string, mixed> $data Collection data (title, description, sort_order, etc.)
     * @return Collection Created Collection DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#create-a-collection
     */
    public function create(array $data): Collection
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            $response = $this->client->post($this->endpoint, $data);

            return Collection::fromArray($response['collection'] ?? []);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Update an existing collection
     *
     * @param int $collectionId Collection ID
     * @param array<string, mixed> $data Collection data to update (title, description, sort_order, etc.)
     * @return Collection Updated Collection DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#update-a-collection
     */
    public function update(int $collectionId, array $data): Collection
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            $response = $this->client->put($this->buildEndpoint((string) $collectionId), $data);

            return Collection::fromArray($response['collection'] ?? []);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * List products in a collection
     *
     * Retrieves all products associated with a collection.
     *
     * @param int $collectionId Collection ID
     * @param array<string, mixed> $queryParams Query parameters (limit, etc.)
     * @return Paginator<\Recharge\Data\Product> Paginator instance for iterating collection products
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#list-collection-products
     */
    public function listProducts(int $collectionId, array $queryParams = []): Paginator
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            return new Paginator(
                client: $this->client,
                endpoint: '/collection_products',
                queryParams: array_merge($queryParams, ['collection_id' => $collectionId]),
                mapper: fn (array $data): \Recharge\Data\Product => \Recharge\Data\Product::fromArray($data),
                itemsKey: 'collection_products'
            );
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Bulk delete products from a collection
     *
     * Removes multiple products from a collection. Limit 250 products per request.
     *
     * @param int $collectionId Collection ID
     * @param array<int> $productIds Array of product IDs to remove
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/collections#bulk-delete-collection-products
     */
    public function deleteProductsBulk(int $collectionId, array $productIds): array
    {
        // Collections require 2021-11 API version
        $originalVersion = $this->client->getApiVersion();
        if ($originalVersion !== ApiVersion::V2021_11) {
            $this->client->setApiVersion(ApiVersion::V2021_11);
        }

        try {
            // Bulk delete uses POST method with product_ids in body
            return $this->client->post($this->buildEndpoint("{$collectionId}/collection_products-bulk"), [
                'product_ids' => $productIds,
            ]);
        } finally {
            // Restore original API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }
}
