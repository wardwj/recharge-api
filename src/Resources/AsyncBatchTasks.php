<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\AsyncBatchTask;
use Recharge\Support\Paginator;

/**
 * Async Batch Tasks resource for interacting with Recharge async batch task endpoints
 *
 * Provides methods to add tasks to a batch and list tasks within a batch.
 * Tasks represent individual operations to be performed within an async batch.
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints
 * @see https://developer.rechargepayments.com/2021-01/async_batch_tasks
 */
class AsyncBatchTasks extends AbstractResource
{
    /**
     * Maximum number of tasks that can be added per request
     */
    private const MAX_TASKS_PER_REQUEST = 1000;

    /**
     * List all tasks in a batch with automatic pagination
     *
     * Returns a Paginator that automatically fetches the next page when iterating.
     * Tasks can be filtered by IDs using the 'ids' query parameter (comma-separated).
     *
     * @param int $batchId Batch ID
     * @param array<string, mixed> $queryParams Query parameters (limit, ids, cursor, etc.)
     *                                           ids: comma-separated list of task IDs to filter
     * @return Paginator<AsyncBatchTask> Paginator instance for iterating tasks
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#list-batch-tasks
     * @see https://developer.rechargepayments.com/2021-01/async_batch_tasks#list-batch-tasks
     */
    public function list(int $batchId, array $queryParams = []): Paginator
    {
        $endpoint = $this->buildEndpoint((string) $batchId . '/tasks');

        // Convert array of IDs to comma-separated string if provided
        if (isset($queryParams['ids']) && is_array($queryParams['ids'])) {
            $queryParams['ids'] = implode(',', $queryParams['ids']);
        }

        return new Paginator(
            client: $this->client,
            endpoint: $endpoint,
            queryParams: $queryParams,
            mapper: fn (array $data): \Recharge\Data\AsyncBatchTask => AsyncBatchTask::fromArray($data),
            itemsKey: 'async_batch_tasks'
        );
    }

    /**
     * Add tasks to a batch
     *
     * Adds tasks to an existing batch. Each task contains a 'body' object with the
     * payload for the operation (e.g., discount data for discount_create batch type).
     *
     * Limitations:
     * - Maximum 1,000 tasks per request
     * - Maximum 10,000 tasks per batch total
     *
     * @param int $batchId Batch ID
     * @param array<int, array<string, mixed>> $tasks Array of task data, each containing a 'body' key
     *                                                 Example: [['body' => ['code' => 'TEST']], ...]
     * @return array<AsyncBatchTask> Array of created AsyncBatchTask DTOs
     * @throws \Recharge\Exceptions\RechargeException
     * @throws \InvalidArgumentException If task count exceeds limits
     * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#add-tasks-to-a-batch
     * @see https://developer.rechargepayments.com/2021-01/async_batch_tasks#create-a-batch-task
     */
    public function addTasks(int $batchId, array $tasks): array
    {
        $taskCount = count($tasks);

        if ($taskCount === 0) {
            throw new \InvalidArgumentException('At least one task must be provided');
        }

        if ($taskCount > self::MAX_TASKS_PER_REQUEST) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Maximum %d tasks per request, %d provided',
                    self::MAX_TASKS_PER_REQUEST,
                    $taskCount
                )
            );
        }

        $endpoint = $this->buildEndpoint((string) $batchId . '/tasks');

        $response = $this->client->post($endpoint, [
            'tasks' => $tasks,
        ]);

        $taskData = $response['async_batch_tasks'] ?? [];

        return array_map(
            fn (array $data): AsyncBatchTask => AsyncBatchTask::fromArray($data),
            $taskData
        );
    }
}
