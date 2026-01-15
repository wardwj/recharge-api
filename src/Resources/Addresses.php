<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Addresses resource for interacting with Recharge address endpoints
 *
 * @package Recharge\Resources
 * @see https://developer.rechargepayments.com/2021-11/addresses
 */
class Addresses extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/addresses';

    /**
     * Addresses constructor
     *
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all addresses
     *
     * @param array<string, mixed> $query Query parameters (limit, page, customer_id, etc.)
     * @return array<int, Address> Array of Address DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#list-addresses
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $addresses = $response['addresses'] ?? [];

        return array_map(function (array $addressData) {
            return DTOFactory::createAddress($this->client, $addressData);
        }, $addresses);
    }

    /**
     * Retrieve a specific address by ID
     *
     * @param int $addressId Address ID
     * @return Address Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#retrieve-an-address
     */
    public function get(int $addressId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$addressId));
        return DTOFactory::createAddress($this->client, $response['address'] ?? []);
    }

    /**
     * Create a new address
     *
     * @param array<string, mixed> $data Address data
     * @return Address Created Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#create-an-address
     */
    public function create(array $data): object
    {
        $response = $this->client->post($this->endpoint, $data);
        return DTOFactory::createAddress($this->client, $response['address'] ?? []);
    }

    /**
     * Update an existing address
     *
     * @param int $addressId Address ID
     * @param array<string, mixed> $data Address data to update
     * @return Address Updated Address DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#update-an-address
     */
    public function update(int $addressId, array $data): object
    {
        $response = $this->client->put($this->buildEndpoint((string)$addressId), $data);
        return DTOFactory::createAddress($this->client, $response['address'] ?? []);
    }

    /**
     * Delete an address
     *
     * @param int $addressId Address ID
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/addresses#delete-an-address
     */
    public function delete(int $addressId): void
    {
        $this->client->delete($this->buildEndpoint((string)$addressId));
    }
}
