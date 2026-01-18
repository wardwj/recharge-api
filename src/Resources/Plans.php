<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Plan;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\PlanSort;
use Recharge\RechargeClient;
use Recharge\Support\Paginator;

/**
 * Plans resource for interacting with Recharge plan endpoints
 *
 * Plans are only available in API version 2021-11.
 * They replace the deprecated products endpoint for plan-related operations in 2021-01.
 *
 * @see https://developer.rechargepayments.com/2021-11/plans
 */
class Plans extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/plans';

    /**
     * Plans constructor
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
        return PlanSort::class;
    }

    /**
     * List all plans
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by type, external_product_id, ids, and date ranges.
     * Supports sorting via sort_by parameter (PlanSort enum or string).
     *
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, type, external_product_id, ids, updated_at_min, updated_at_max, sort_by, cursor, etc.)
     *                                           sort_by can be a PlanSort enum or a string value
     * @return Paginator<Plan> Paginator instance for iterating plans
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/plans#list-plans
     */
    public function list(array $queryParams = []): Paginator
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $queryParams = $this->validateSort($queryParams);

            return new Paginator(
                client: $this->client,
                endpoint: $this->endpoint,
                queryParams: $queryParams,
                mapper: fn (array $data): \Recharge\Data\Plan => Plan::fromArray($data),
                itemsKey: 'plans'
            );
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Retrieve a specific plan by ID
     *
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param int $id Plan ID
     * @return Plan Plan DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/plans#retrieve-a-plan
     */
    public function get(int $id): Plan
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->get($this->buildEndpoint((string) $id));

            return Plan::fromArray($response['plan'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Create a new plan
     *
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param array<string, mixed> $data Plan creation data (external_product_id, type, title, subscription_preferences, discount_amount, discount_type, sort_order, channel_settings, etc.)
     * @return Plan Created Plan DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/plans#create-a-plan
     */
    public function create(array $data): Plan
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->post($this->endpoint, $data);

            return Plan::fromArray($response['plan'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Update an existing plan
     *
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param int $id Plan ID
     * @param array<string, mixed> $data Plan update data
     * @return Plan Updated Plan DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/plans#update-a-plan
     */
    public function update(int $id, array $data): Plan
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            $response = $this->client->put($this->buildEndpoint((string) $id), $data);

            return Plan::fromArray($response['plan'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Delete a plan
     *
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param int $id Plan ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/plans#delete-a-plan
     */
    public function delete(int $id): void
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
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

    /**
     * Bulk create plans for a product
     *
     * Creates multiple plans for a product in a single request (up to 20 plans).
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param string|array<string, mixed> $externalProductId External product ID (string or object with ecommerce platform)
     * @param array<int, array<string, mixed>> $plans Array of plan data arrays (up to 20)
     * @return array<Plan> Array of created Plan DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If more than 20 plans are provided
     * @see https://developer.rechargepayments.com/2021-11/plans#bulk-create-plans
     */
    public function createBulk(string|array $externalProductId, array $plans): array
    {
        if (count($plans) > 20) {
            throw new \InvalidArgumentException('Maximum 20 plans can be created in a single bulk operation');
        }

        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            // Convert external_product_id to string if it's an object
            if (is_array($externalProductId)) {
                $productId = isset($externalProductId['ecommerce']) && isset($externalProductId['product_id'])
                    ? (string) $externalProductId['product_id']
                    : (string) json_encode($externalProductId);
            } else {
                $productId = (string) $externalProductId;
            }

            $response = $this->client->post(
                "/products/{$productId}/plans-bulk",
                ['plans' => $plans]
            );

            $planData = $response['plans'] ?? [];
            $result = [];
            foreach ($planData as $plan) {
                $result[] = Plan::fromArray($plan);
            }

            return $result;
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Bulk update plans for a product
     *
     * Updates multiple plans for a product in a single request (up to 20 plans).
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param string|array<string, mixed> $externalProductId External product ID (string or object with ecommerce platform)
     * @param array<int, array<string, mixed>> $plans Array of plan data arrays with id (up to 20)
     * @return array<Plan> Array of updated Plan DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If more than 20 plans are provided
     * @see https://developer.rechargepayments.com/2021-11/plans#bulk-update-plans
     */
    public function updateBulk(string|array $externalProductId, array $plans): array
    {
        if (count($plans) > 20) {
            throw new \InvalidArgumentException('Maximum 20 plans can be updated in a single bulk operation');
        }

        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            // Convert external_product_id to string if it's an object
            if (is_array($externalProductId)) {
                $productId = isset($externalProductId['ecommerce']) && isset($externalProductId['product_id'])
                    ? (string) $externalProductId['product_id']
                    : (string) json_encode($externalProductId);
            } else {
                $productId = (string) $externalProductId;
            }

            $response = $this->client->put(
                "/products/{$productId}/plans-bulk",
                ['plans' => $plans]
            );

            $planData = $response['plans'] ?? [];
            $result = [];
            foreach ($planData as $plan) {
                $result[] = Plan::fromArray($plan);
            }

            return $result;
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Bulk delete plans for a product
     *
     * Deletes multiple plans for a product in a single request.
     * Plans are only available in API version 2021-11.
     * This method automatically switches to 2021-11, makes the request, then restores the original version.
     *
     * @param string|array<string, mixed> $externalProductId External product ID (string or object with ecommerce platform)
     * @param array<int> $planIds Array of plan IDs to delete
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/plans#bulk-delete-plans
     */
    public function deleteBulk(string|array $externalProductId, array $planIds): void
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Plans require 2021-11 API version
            if ($originalVersion !== ApiVersion::V2021_11) {
                $this->client->setApiVersion(ApiVersion::V2021_11);
            }

            // Convert external_product_id to string if it's an object
            if (is_array($externalProductId)) {
                $productId = isset($externalProductId['ecommerce']) && isset($externalProductId['product_id'])
                    ? (string) $externalProductId['product_id']
                    : (string) json_encode($externalProductId);
            } else {
                $productId = (string) $externalProductId;
            }

            // Bulk delete uses DELETE with plan_ids as query parameter
            $planIdsQuery = http_build_query(['plan_ids' => implode(',', array_map('strval', $planIds))]);
            $this->client->delete("/products/{$productId}/plans-bulk?{$planIdsQuery}");
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }
}
