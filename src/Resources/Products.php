<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Products resource for interacting with Recharge product endpoints
 *
 * @package Recharge\Resources
 * @see https://developer.rechargepayments.com/2021-11/products
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
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all products
     *
     * @param array<string, mixed> $query Query parameters (limit, page, etc.)
     * @return array<int, Product> Array of Product DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#list-products
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $products = $response['products'] ?? [];

        return array_map(function (array $productData) {
            return DTOFactory::createProduct($this->client, $productData);
        }, $products);
    }

    /**
     * Retrieve a specific product by ID
     *
     * @param int $productId Product ID
     * @return Product Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#retrieve-a-product
     */
    public function get(int $productId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$productId));
        return DTOFactory::createProduct($this->client, $response['product'] ?? []);
    }

    /**
     * Create a new product
     *
     * @param array<string, mixed> $data Product data
     * @return Product Created Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#create-a-product
     */
    public function create(array $data): object
    {
        $response = $this->client->post($this->endpoint, $data);
        return DTOFactory::createProduct($this->client, $response['product'] ?? []);
    }

    /**
     * Update an existing product
     *
     * @param int $productId Product ID
     * @param array<string, mixed> $data Product data to update
     * @return Product Updated Product DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#update-a-product
     */
    public function update(int $productId, array $data): object
    {
        $response = $this->client->put($this->buildEndpoint((string)$productId), $data);
        return DTOFactory::createProduct($this->client, $response['product'] ?? []);
    }

    /**
     * Delete a product
     *
     * @param int $productId Product ID
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/products#delete-a-product
     */
    public function delete(int $productId): void
    {
        $this->client->delete($this->buildEndpoint((string)$productId));
    }
}
