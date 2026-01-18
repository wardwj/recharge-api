<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\AsyncBatch;
use Recharge\Enums\AsyncBatchType;
use Recharge\Enums\Sort\AsyncBatchSort;
use Recharge\Support\Paginator;

/**
 * Async Batches resource for interacting with Recharge async batch endpoints
 *
 * Provides methods to create, retrieve, list, and process async batches.
 * Async batches allow you to perform bulk operations efficiently.
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints
 * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints
 */
class AsyncBatches extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/async_batches';

    /**
     * Get the sort enum class for this resource
     */
    protected function getSortEnumClass(): ?string
    {
        return AsyncBatchSort::class;
    }

    /**
     * List all async batches with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Supports sorting via sort_by parameter (AsyncBatchSort enum or string).
     *
     * @param array<string, mixed> $queryParams Query parameters (limit, sort_by, cursor, etc.)
     *                                           sort_by can be an AsyncBatchSort enum or a string value
     * @return Paginator<AsyncBatch> Paginator instance for iterating async batches
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If sort_by value is invalid
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#list-batches
     * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#list-batches
     */
    public function list(array $queryParams = []): Paginator
    {
        $queryParams = $this->validateSort($queryParams);

        return new Paginator(
            client: $this->client,
            endpoint: $this->endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\AsyncBatch => AsyncBatch::fromArray($data),
            itemsKey: 'async_batches'
        );
    }

    /**
     * Retrieve a specific async batch by ID
     *
     * @param int $batchId Batch ID
     * @return AsyncBatch AsyncBatch DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#retrieve-a-batch
     * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#retrieve-a-batch
     */
    public function get(int $batchId): AsyncBatch
    {
        $response = $this->client->get($this->buildEndpoint((string) $batchId));

        return AsyncBatch::fromArray($response['async_batch'] ?? []);
    }

    /**
     * Create a new async batch
     *
     * Creates a new batch for a specific batch type. The batch type must be valid
     * for the current API version. After creating, add tasks using addTasks(), then
     * process using process().
     *
     * @param AsyncBatchType $batchType Type of batch operation
     * @return AsyncBatch Created AsyncBatch DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If batch type is not valid for current API version
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#create-a-batch
     * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#create-a-batch
     */
    public function create(AsyncBatchType $batchType): AsyncBatch
    {
        $currentVersion = $this->client->getApiVersion();

        if (!$batchType->isAvailableIn($currentVersion)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Batch type "%s" is not available in API version %s',
                    $batchType->value,
                    $currentVersion->value
                )
            );
        }

        $response = $this->client->post($this->endpoint, [
            'batch_type' => $batchType->value,
        ]);

        return AsyncBatch::fromArray($response['async_batch'] ?? []);
    }

    /**
     * Process an async batch
     *
     * Submits the batch for processing. Once processed, tasks in the batch
     * will be executed. Use this after adding all tasks to the batch.
     *
     * @param int $batchId Batch ID
     * @return AsyncBatch Updated AsyncBatch DTO with processing status
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#process-a-batch
     * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#process-a-batch
     */
    public function process(int $batchId): AsyncBatch
    {
        $response = $this->client->post($this->buildEndpoint((string) $batchId . '/process'), []);

        return AsyncBatch::fromArray($response['async_batch'] ?? []);
    }
}
