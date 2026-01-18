<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\PaymentMethod;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\PaymentMethodSort;
use Recharge\RechargeClient;
use Recharge\Support\Paginator;

/**
 * Payment Methods resource for interacting with Recharge payment method endpoints
 *
 * Payment methods are only available in API version 2021-11.
 * Payment sources in 2021-01 are deprecated and use a different endpoint structure.
 * This resource automatically switches to 2021-11 when needed.
 *
 * Note: Payment methods require specific API token scopes:
 * - read_payment_methods for read operations
 * - write_payment_methods for create/update/delete operations
 *
 * @see https://developer.rechargepayments.com/2021-11/payment_methods
 */
class PaymentMethods extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/payment_methods';

    /**
     * PaymentMethods constructor
     *
     * @param RechargeClient $client The Recharge API client instance
     */
    public function __construct(RechargeClient $client)
    {
        parent::__construct($client);
    }

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return PaymentMethodSort::class;
    }

    /**
     * List all payment methods
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by customer_id and include parameter for nested objects.
     * Supports sorting via sort_by parameter (PaymentMethodSort enum or string).
     *
     * Payment methods are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, customer_id, include, sort_by, cursor, etc.)
     *                                           sort_by can be a PaymentMethodSort enum or a string value
     *                                           include can be 'addresses' to include billing addresses
     * @return Paginator<PaymentMethod> Paginator instance for iterating payment methods
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/payment_methods#list-payment-methods
     */
    public function list(array $queryParams = []): Paginator
    {
        $context = $this->switchToVersion(ApiVersion::V2021_11);

        try {
            $queryParams = $this->validateSort($queryParams);

            return new Paginator(
                client: $this->client,
                endpoint: $this->endpoint,
                queryParams: $queryParams,
                mapper: fn (array $data): \Recharge\Data\PaymentMethod => PaymentMethod::fromArray($data),
                itemsKey: 'payment_methods'
            );
        } finally {
            $context->restore();
        }
    }

    /**
     * Retrieve a specific payment method by ID
     *
     * Payment methods are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param int $id Payment Method ID
     * @return PaymentMethod Payment Method DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/payment_methods#retrieve-a-payment-method
     */
    public function get(int $id): PaymentMethod
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Payment methods require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->get($this->buildEndpoint((string) $id));

            return PaymentMethod::fromArray($response['payment_method'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Create a new payment method
     *
     * Payment methods are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $data Payment method creation data (customer_id, payment_type, processor_name, processor_customer_token, processor_payment_method_token, default, billing_address, etc.)
     * @return PaymentMethod Created Payment Method DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/payment_methods#create-a-payment-method
     */
    public function create(array $data): PaymentMethod
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Payment methods require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->post($this->endpoint, $data);

            return PaymentMethod::fromArray($response['payment_method'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Update an existing payment method
     *
     * Payment methods are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * Note: Many fields cannot be updated (e.g., card number, expiry).
     * Typically only default status and billing_address can be updated.
     * For shopify_payments processor, updates are read-only.
     *
     * @param int $id Payment Method ID
     * @param array<string, mixed> $data Payment method update data (default, billing_address, etc.)
     * @return PaymentMethod Updated Payment Method DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/payment_methods#update-a-payment-method
     */
    public function update(int $id, array $data): PaymentMethod
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Payment methods require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->put($this->buildEndpoint((string) $id), $data);

            return PaymentMethod::fromArray($response['payment_method'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Delete a payment method
     *
     * Payment methods are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * Note: Payment methods can only be deleted if they are not in use by active subscriptions.
     *
     * @param int $id Payment Method ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/payment_methods#delete-a-payment-method
     */
    public function delete(int $id): void
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Payment methods require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $this->client->delete($this->buildEndpoint((string) $id));
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }
}
