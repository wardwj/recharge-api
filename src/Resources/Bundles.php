<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Bundle;
use Recharge\Enums\Sort\BundleSort;
use Recharge\Support\Paginator;

/**
 * Bundles resource for interacting with Recharge bundle selection endpoints
 *
 * Provides methods to list, retrieve, create, update, and delete bundle selections.
 * Bundle selections represent customizable product bundles available for subscription.
 *
 * @see https://developer.rechargepayments.com/2021-11/bundle_selections
 * @see https://developer.rechargepayments.com/2021-01/bundle_selections
 */
class Bundles extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/bundle_selections';

    /**
     * List all bundles with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports sorting via sort_by parameter (BundleSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, sort_by, etc.)
     *                                           sort_by can be a BundleSort enum or a string value
     * @return Paginator<Bundle> Paginator instance for iterating bundles
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/bundle_selections#list-bundle-selections
     */
    public function list(array $queryParams = []): Paginator
    {
        // Convert enum to string if provided
        if (isset($queryParams['sort_by']) && $queryParams['sort_by'] instanceof BundleSort) {
            $queryParams['sort_by'] = $queryParams['sort_by']->value;
        }

        // Validate sort_by string if provided
        if (isset($queryParams['sort_by']) && is_string($queryParams['sort_by'])) {
            if (BundleSort::tryFromString($queryParams['sort_by']) === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid sort_by value "%s". Allowed values: %s',
                        $queryParams['sort_by'],
                        implode(', ', array_column(BundleSort::cases(), 'value'))
                    )
                );
            }
        }

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\Bundle => Bundle::fromArray($data),
            itemsKey: 'bundle_selections'
        );
    }

    /**
     * Retrieve a specific bundle by ID
     *
     * @param int $bundleId Bundle ID
     * @return Bundle Bundle DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/bundle_selections#retrieve-a-bundle-selection
     */
    public function get(int $bundleId): Bundle
    {
        $response = $this->client->get($this->buildEndpoint((string) $bundleId));

        return Bundle::fromArray($response['bundle_selection'] ?? []);
    }

    /**
     * Create a new bundle
     *
     * @param array<string, mixed> $data Bundle data
     * @return Bundle Created Bundle DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/bundle_selections#create-a-bundle-selection
     */
    public function create(array $data): Bundle
    {
        $response = $this->client->post($this->endpoint, $data);

        return Bundle::fromArray($response['bundle_selection'] ?? []);
    }

    /**
     * Update an existing bundle
     *
     * @param int $bundleId Bundle ID
     * @param array<string, mixed> $data Bundle data to update
     * @return Bundle Updated Bundle DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/bundle_selections#update-a-bundle-selection
     */
    public function update(int $bundleId, array $data): Bundle
    {
        $response = $this->client->put($this->buildEndpoint((string) $bundleId), $data);

        return Bundle::fromArray($response['bundle_selection'] ?? []);
    }

    /**
     * Delete a bundle
     *
     * Permanently deletes a bundle. This action cannot be undone.
     *
     * @param int $bundleId Bundle ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/bundle_selections#delete-a-bundle-selection
     */
    public function delete(int $bundleId): void
    {
        $this->client->delete($this->buildEndpoint((string) $bundleId));
    }
}
