<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\OneTime;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\OneTimeSort;
use Recharge\RechargeClient;
use Recharge\Support\Paginator;

/**
 * OneTimes resource for interacting with Recharge one-time purchase endpoints
 *
 * One-times are non-recurring line items attached to a QUEUED charge.
 * Supports both API versions 2021-01 and 2021-11.
 *
 * Note: In API version 2021-01, creating onetimes requires an address_id in the path:
 * POST /addresses/{address_id}/onetimes
 * In API version 2021-11, use: POST /onetimes
 *
 * @see https://developer.rechargepayments.com/2021-11/onetimes
 * @see https://developer.rechargepayments.com/2021-01/onetimes
 */
class OneTimes extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/onetimes';

    /**
     * OneTimes constructor
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
        return OneTimeSort::class;
    }

    /**
     * List all one-times
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports filtering by address_id, customer_id, external_variant_id, and date ranges.
     * Supports sorting via sort_by parameter (OneTimeSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, address_id, customer_id, external_variant_id, created_at_min, created_at_max, updated_at_min, updated_at_max, include_cancelled, sort_by, etc.)
     *                                           sort_by can be a OneTimeSort enum or a string value
     * @return Paginator<OneTime> Paginator instance for iterating one-times
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/onetimes#list-onetimes
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\OneTime => OneTime::fromArray($data),
            itemsKey: 'onetimes'
        );
    }

    /**
     * Retrieve a specific one-time by ID
     *
     * @param int $id OneTime ID
     * @return OneTime OneTime DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/onetimes#retrieve-a-onetime
     */
    public function get(int $id): OneTime
    {
        $response = $this->client->get($this->buildEndpoint((string) $id));

        return OneTime::fromArray($response['onetime'] ?? []);
    }

    /**
     * Create a new one-time
     *
     * In API version 2021-01, this requires an address_id in the path.
     * In API version 2021-11, address_id can be provided in the request body.
     * This method automatically handles version differences.
     *
     * @param array<string, mixed> $data OneTime creation data (address_id, external_variant_id, quantity, price, etc.)
     * @return OneTime Created OneTime DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/onetimes#create-a-onetime
     * @see https://developer.rechargepayments.com/2021-01/onetimes#create-a-onetime
     */
    public function create(array $data): OneTime
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // In 2021-01, creation requires address_id in the path
            if ($originalVersion === ApiVersion::V2021_01) {
                if (!isset($data['address_id'])) {
                    throw new \InvalidArgumentException('address_id is required for creating onetimes in API version 2021-01');
                }

                $addressId = (int) $data['address_id'];
                $bodyData = $data;
                unset($bodyData['address_id']);

                $response = $this->client->post("/addresses/{$addressId}/onetimes", $bodyData);
            } else {
                // 2021-11 uses the standard endpoint
                $response = $this->client->post($this->endpoint, $data);
            }

            return OneTime::fromArray($response['onetime'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Update an existing one-time
     *
     * @param int $id OneTime ID
     * @param array<string, mixed> $data OneTime update data
     * @return OneTime Updated OneTime DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/onetimes#update-a-onetime
     */
    public function update(int $id, array $data): OneTime
    {
        $response = $this->client->put($this->buildEndpoint((string) $id), $data);

        return OneTime::fromArray($response['onetime'] ?? []);
    }

    /**
     * Delete a one-time
     *
     * @param int $id OneTime ID
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/onetimes#delete-a-onetime
     */
    public function delete(int $id): void
    {
        $this->client->delete($this->buildEndpoint((string) $id));
    }
}
