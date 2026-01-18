<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;

/**
 * Async Batch Task Data Transfer Object
 *
 * Immutable DTO representing a task within an async batch.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints
 * @see https://developer.rechargepayments.com/2021-01/async_batch_tasks#the-async-batch-tasks-object
 */
final readonly class AsyncBatchTask implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the task
     * @param int $batchId ID of the batch this task belongs to
     * @param array<string, mixed> $body Task payload (the data to be processed)
     * @param string|null $status Task status (e.g., 'queued', 'processing', 'success', 'failed')
     * @param array<string, mixed>|null $result Task result/output (present when completed)
     * @param int|null $statusCode HTTP status code from the task operation
     * @param CarbonImmutable|null $createdAt When the task was created
     * @param CarbonImmutable|null $queuedAt When the task was queued for processing
     * @param CarbonImmutable|null $startedAt When the task started processing
     * @param CarbonImmutable|null $completedAt When the task completed (success or failure)
     * @param CarbonImmutable|null $deletedAt When the task was deleted
     * @param CarbonImmutable|null $updatedAt Last update timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $batchId,
        public array $body = [],
        public ?string $status = null,
        public ?array $result = null,
        public ?int $statusCode = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $queuedAt = null,
        public ?CarbonImmutable $startedAt = null,
        public ?CarbonImmutable $completedAt = null,
        public ?CarbonImmutable $deletedAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     *
     * @param array<string, mixed> $data Task data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            batchId: (int) ($data['batch_id'] ?? 0),
            body: $data['body'] ?? [],
            status: $data['status'] ?? null,
            result: $data['result'] ?? null,
            statusCode: isset($data['result']['status_code']) ? (int) $data['result']['status_code'] : null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            queuedAt: isset($data['queued_at'])
                ? CarbonImmutable::parse($data['queued_at'])
                : null,
            startedAt: isset($data['started_at'])
                ? CarbonImmutable::parse($data['started_at'])
                : null,
            completedAt: isset($data['completed_at'])
                ? CarbonImmutable::parse($data['completed_at'])
                : null,
            deletedAt: isset($data['deleted_at'])
                ? CarbonImmutable::parse($data['deleted_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            rawData: $data
        );
    }

    /**
     * Convert to array for serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batchId,
            'body' => $this->body,
            'status' => $this->status,
            'result' => $this->result,
            'created_at' => $this->createdAt?->toIso8601String(),
            'queued_at' => $this->queuedAt?->toIso8601String(),
            'started_at' => $this->startedAt?->toIso8601String(),
            'completed_at' => $this->completedAt?->toIso8601String(),
            'deleted_at' => $this->deletedAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ];
    }

    /**
     * Get raw data from API response
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    /**
     * Check if task has completed successfully
     *
     * @return bool True if task status is 'success' and has result
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success' && $this->result !== null;
    }

    /**
     * Check if task has failed
     *
     * @return bool True if task status is 'failed'
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}
