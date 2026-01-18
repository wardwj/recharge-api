<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Customer;
use Recharge\Enums\Sort\CustomerSort;
use Recharge\Support\Paginator;

/**
 * Customers resource for interacting with Recharge customer endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete customers,
 * as well as access to customer-specific endpoints like delivery schedules
 * and credit summaries.
 *
 * @see https://developer.rechargepayments.com/2021-11/customers
 */
class Customers extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/customers';

    /**
     * List all customers with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by created_at, updated_at, email, and more.
     * Supports sorting via sort_by parameter (CustomerSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, created_at_min, updated_at_min, email, sort_by, etc.)
     *                                           sort_by can be a CustomerSort enum or a string value
     * @return Paginator<Customer> Paginator instance for iterating customers
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/customers#list-customers
     */
    public function list(array $queryParams = []): Paginator
    {
        // Convert enum to string if provided
        if (isset($queryParams['sort_by']) && $queryParams['sort_by'] instanceof CustomerSort) {
            $queryParams['sort_by'] = $queryParams['sort_by']->value;
        }

        // Validate sort_by string if provided
        if (isset($queryParams['sort_by']) && is_string($queryParams['sort_by'])) {
            if (CustomerSort::tryFromString($queryParams['sort_by']) === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid sort_by value "%s". Allowed values: %s',
                        $queryParams['sort_by'],
                        implode(', ', array_column(CustomerSort::cases(), 'value'))
                    )
                );
            }
        }

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Customer => Customer::fromArray($data),
            itemsKey: 'customers'
        );
    }

    /**
     * Retrieve a specific customer by ID
     *
     * @param int $customerId Customer ID
     * @return Customer Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#retrieve-a-customer
     */
    public function get(int $customerId): Customer
    {
        $response = $this->client->get($this->buildEndpoint((string) $customerId));

        return Customer::fromArray($response['customer'] ?? []);
    }

    /**
     * Create a new customer
     *
     * @param array<string, mixed> $data Customer data (email, first_name, last_name, etc.)
     * @return Customer Created Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#create-a-customer
     */
    public function create(array $data): Customer
    {
        $response = $this->client->post($this->endpoint, $data);

        return Customer::fromArray($response['customer'] ?? []);
    }

    /**
     * Update an existing customer
     *
     * @param int $customerId Customer ID
     * @param array<string, mixed> $data Customer data to update (first_name, last_name, email, etc.)
     * @return Customer Updated Customer DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#update-a-customer
     */
    public function update(int $customerId, array $data): Customer
    {
        $response = $this->client->put($this->buildEndpoint((string) $customerId), $data);

        return Customer::fromArray($response['customer'] ?? []);
    }

    /**
     * Delete a customer
     *
     * Permanently deletes a customer. This action cannot be undone.
     *
     * @param int $customerId Customer ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/customers#delete-a-customer
     */
    public function delete(int $customerId): void
    {
        $this->client->delete($this->buildEndpoint((string) $customerId));
    }

    /**
     * Retrieve a customer's delivery schedule
     *
     * Returns the delivery schedule for a customer, including upcoming charges
     * and their associated subscription details.
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
     * Returns the credit summary for a customer, including store credit balance
     * and credit history.
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

    /**
     * Send a notification to a customer
     *
     * Sends an email notification to a customer using a configured template.
     * Supported templates: "get_account_access" and "upcoming_charge".
     * Template variables can be provided via template_vars array if required by the template.
     *
     * @param int $customerId Customer ID
     * @param string|\Recharge\Enums\NotificationTemplate $template Notification template name
     * @param array<string, mixed> $templateVars Optional template variables (if template requires them)
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If template value is invalid
     * @see https://developer.rechargepayments.com/2021-11/customers#send-a-notification
     * @see https://developer.rechargepayments.com/2021-01/customers#send-a-notification
     */
    public function sendNotification(int $customerId, string|\Recharge\Enums\NotificationTemplate $template, array $templateVars = []): array
    {
        // Convert enum to string if provided
        if ($template instanceof \Recharge\Enums\NotificationTemplate) {
            $template = $template->value;
        }

        // Validate template string if provided
        if (is_string($template)) {
            if (\Recharge\Enums\NotificationTemplate::tryFromString($template) === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid notification template "%s". Supported templates: %s',
                        $template,
                        implode(', ', array_column(\Recharge\Enums\NotificationTemplate::cases(), 'value'))
                    )
                );
            }
        }

        $data = ['template' => $template];
        if (!empty($templateVars)) {
            $data['template_vars'] = $templateVars;
        }

        $response = $this->client->post(
            $this->buildEndpoint("{$customerId}/notifications"),
            $data
        );

        return $response;
    }
}
