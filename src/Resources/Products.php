<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Product;
use Recharge\Support\Paginator;

/**
 * Products resource for interacting with Recharge product endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete products.
 * Products represent the items available for subscription in your store.
 *
 * @see https://developer.rechargepayments.com/2021-11/products
 */
class Products extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/products';

    /**
     * List all products with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, etc.)
     * @return Paginator<Product> Paginator instance for iterating products
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#list-products
     */
    public function list(array $queryParams = []): Paginator
    {
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
     * @param int $productId Product ID
     * @return Product Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#retrieve-a-product
     */
    public function get(int $productId): Product
    {
        $response = $this->client->get($this->buildEndpoint((string) $productId));

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Create a new product
     *
     * @param array<string, mixed> $data Product data (title, price, subscription_defaults, etc.)
     * @return Product Created Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#create-a-product
     */
    public function create(array $data): Product
    {
        $response = $this->client->post($this->endpoint, $data);

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Update an existing product
     *
     * @param int $productId Product ID
     * @param array<string, mixed> $data Product data to update (title, price, subscription_defaults, etc.)
     * @return Product Updated Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#update-a-product
     */
    public function update(int $productId, array $data): Product
    {
        $response = $this->client->put($this->buildEndpoint((string) $productId), $data);

        return Product::fromArray($response['product'] ?? []);
    }

    /**
     * Delete a product
     *
     * Permanently deletes a product. This action cannot be undone.
     *
     * @param int $productId Product ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#delete-a-product
     */
    public function delete(int $productId): void
    {
        $this->client->delete($this->buildEndpoint((string) $productId));
    }
}
