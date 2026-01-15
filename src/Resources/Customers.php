<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Customers resource for interacting with Recharge customer endpoints
 *
 * @package Recharge\Resources
 * @see https://developer.rechargepayments.com/2021-11/customers
 */
class Customers extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/customers';

    /**
     * Customers constructor
     *
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * List all customers
     *
     * @param array<string, mixed> $query Query parameters (limit, page, created_at_min, etc.)
     * @return array<int, Customer> Array of Customer DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#list-customers
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get($this->endpoint, $query);
        $customers = $response['customers'] ?? [];

        return array_map(function (array $customerData) {
            return DTOFactory::createCustomer($this->client, $customerData);
        }, $customers);
    }

    /**
     * Retrieve a specific customer by ID
     *
     * @param int $customerId Customer ID
     * @return Customer Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#retrieve-a-customer
     */
    public function get(int $customerId): object
    {
        $response = $this->client->get($this->buildEndpoint((string)$customerId));
        return DTOFactory::createCustomer($this->client, $response['customer'] ?? []);
    }

    /**
     * Create a new customer
     *
     * @param array<string, mixed> $data Customer data
     * @return Customer Created Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#create-a-customer
     */
    public function create(array $data): object
    {
        $response = $this->client->post($this->endpoint, $data);
        return DTOFactory::createCustomer($this->client, $response['customer'] ?? []);
    }

    /**
     * Update an existing customer
     *
     * @param int $customerId Customer ID
     * @param array<string, mixed> $data Customer data to update
     * @return Customer Updated Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#update-a-customer
     */
    public function update(int $customerId, array $data): object
    {
        $response = $this->client->put($this->buildEndpoint((string)$customerId), $data);
        return DTOFactory::createCustomer($this->client, $response['customer'] ?? []);
    }

    /**
     * Delete a customer
     *
     * @param int $customerId Customer ID
     * @return void
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#delete-a-customer
     */
    public function delete(int $customerId): void
    {
        $this->client->delete($this->buildEndpoint((string)$customerId));
    }

    /**
     * Retrieve a customer's delivery schedule
     *
     * @param int $customerId Customer ID
     * @return array<string, mixed> Delivery schedule data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#retrieve-a-customer-delivery-schedule
     */
    public function getDeliverySchedule(int $customerId): array
    {
        $response = $this->client->get($this->buildEndpoint("{$customerId}/delivery_schedule"));
        return $response['delivery_schedule'] ?? [];
    }

    /**
     * Retrieve a customer's credit summary
     *
     * @param int $customerId Customer ID
     * @return array<string, mixed> Credit summary data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#retrieve-a-customers-credit-summary
     */
    public function getCreditSummary(int $customerId): array
    {
        $response = $this->client->get($this->buildEndpoint("{$customerId}/credit_summary"));
        return $response['credit_summary'] ?? [];
    }
}
