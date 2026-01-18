<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Product;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\ProductSort;
use Recharge\RechargeClient;
use Recharge\Support\Paginator;

/**
 * Products resource for interacting with Recharge product endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete products.
 * Products represent the items available for subscription in your store.
 *
 * Note: Products in API version 2021-01 are deprecated as of June 30, 2025.
 * The recommended replacement is using Plans in 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/products
 * @see https://developer.rechargepayments.com/2021-01/products
 */
class Products extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/products';

    /**
     * Products constructor
     *
     * @param RechargeClient $client The Recharge API client instance
     */
    public function __construct(RechargeClient $client)
    {
        parent::__construct($client);
    }

    /**
     * List all products with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by external_product_id, shopify_product_ids, and more.
     * Supports sorting via sort_by parameter (ProductSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, external_product_id, shopify_product_ids, sort_by, etc.)
     *                                           sort_by can be a ProductSort enum or a string value
     * @return Paginator<Product> Paginator instance for iterating products
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/products#list-products
     */
    public function list(array $queryParams = []): Paginator
    {
        // Convert enum to string if provided
        if (isset($queryParams['sort_by']) && $queryParams['sort_by'] instanceof ProductSort) {
            $queryParams['sort_by'] = $queryParams['sort_by']->value;
        }

        // Validate sort_by string if provided
        if (isset($queryParams['sort_by']) && is_string($queryParams['sort_by'])) {
            if (ProductSort::tryFromString($queryParams['sort_by']) === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid sort_by value "%s". Allowed values: %s',
                        $queryParams['sort_by'],
                        implode(', ', array_column(ProductSort::cases(), 'value'))
                    )
                );
            }
        }

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Product => Product::fromArray($data),
            itemsKey: 'products'
        );
    }

    /**
     * Retrieve a specific product by ID
     *
     * In API version 2021-11, use external_product_id (string).
     * In API version 2021-01, use numeric id.
     * This method automatically handles version differences.
     *
     * @param int|string $productId Product ID (numeric for 2021-01, external_product_id string for 2021-11)
     * @return Product Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#retrieve-a-product
     * @see https://developer.rechargepayments.com/2021-01/products#retrieve-a-product
     */
    public function get(int|string $productId): Product
    {
        $response = $this->client->get($this->buildEndpoint((string) $productId));

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Create a new product
     *
     * @param array<string, mixed> $data Product data (title, vendor, variants, images, etc.)
     * @return Product Created Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#create-a-product
     * @see https://developer.rechargepayments.com/2021-01/products#create-a-product
     */
    public function create(array $data): Product
    {
        $response = $this->client->post($this->endpoint, $data);

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Update an existing product
     *
     * In API version 2021-11, use external_product_id (string).
     * In API version 2021-01, use numeric id.
     * This method automatically handles version differences.
     *
     * @param int|string $productId Product ID (numeric for 2021-01, external_product_id string for 2021-11)
     * @param array<string, mixed> $data Product data to update (title, vendor, variants, images, etc.)
     * @return Product Updated Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#update-a-product
     * @see https://developer.rechargepayments.com/2021-01/products#update-a-product
     */
    public function update(int|string $productId, array $data): Product
    {
        $response = $this->client->put($this->buildEndpoint((string) $productId), $data);

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Delete a product
     *
     * Permanently deletes a product. This action cannot be undone.
     * In API version 2021-11, use external_product_id (string).
     * In API version 2021-01, use numeric id.
     * This method automatically handles version differences.
     *
     * @param int|string $productId Product ID (numeric for 2021-01, external_product_id string for 2021-11)
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#delete-a-product
     * @see https://developer.rechargepayments.com/2021-01/products#delete-a-product
     */
    public function delete(int|string $productId): void
    {
        $this->client->delete($this->buildEndpoint((string) $productId));
    }

    /**
     * Get count of products
     *
     * Count endpoint is only available in API version 2021-01.
     * This method temporarily switches to 2021-01, makes the request, then restores the original version.
     *
     * Note: Count may not be reliable for all filter combinations in 2021-11.
     *
     * @param array<string, mixed> $queryParams Query parameters for filtering
     * @return int Count of products matching the filters
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/products#count-products
     */
    public function count(array $queryParams = []): int
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Count endpoint requires 2021-01 API version
            $this->client->setApiVersion(ApiVersion::V2021_01);
            $response = $this->client->get($this->buildEndpoint('count'), $queryParams);

            return (int) ($response['count'] ?? 0);
        } finally {
            // Restore original API version
            $this->client->setApiVersion($originalVersion);
        }
    }
}
