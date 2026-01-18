<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;
use Recharge\Enums\AsyncBatchStatus;
use Recharge\Enums\AsyncBatchType;

/**
 * Async Batch Data Transfer Object
 *
 * Immutable DTO representing a Recharge async batch.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#the-async-batch-object
 * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#the-async-batch-object
 */
final readonly class AsyncBatch implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Async Batch
     * @param AsyncBatchType $batchType Type of batch operation
     * @param AsyncBatchStatus $status Current status of the batch
     * @param string|null $version API version used for this batch (e.g., '2021-11')
     * @param int|null $totalTaskCount Total number of tasks in the batch
     * @param int|null $successTaskCount Number of successfully completed tasks
     * @param int|null $failTaskCount Number of failed tasks
     * @param CarbonImmutable|null $createdAt When the batch was created
     * @param CarbonImmutable|null $submittedAt When the batch was submitted for processing
     * @param CarbonImmutable|null $closedAt When the batch was closed (completed or failed)
     * @param CarbonImmutable|null $updatedAt Last update timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public AsyncBatchType $batchType,
        public AsyncBatchStatus $status,
        public ?string $version = null,
        public ?int $totalTaskCount = null,
        public ?int $successTaskCount = null,
        public ?int $failTaskCount = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $submittedAt = null,
        public ?CarbonImmutable $closedAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     *
     * @param array<string, mixed> $data Async batch data from API
     */
    public static function fromArray(array $data): static
    {
        $batchType = isset($data['batch_type'])
            ? AsyncBatchType::tryFrom($data['batch_type'])
            : null;

        $status = isset($data['status'])
            ? AsyncBatchStatus::tryFrom($data['status'])
            : null;

        return new self(
            id: (int) ($data['id'] ?? 0),
            batchType: $batchType ?? AsyncBatchType::DISCOUNT_CREATE, // Default fallback
            status: $status ?? AsyncBatchStatus::NOT_STARTED, // Default fallback
            version: $data['version'] ?? null,
            totalTaskCount: isset($data['total_task_count']) ? (int) $data['total_task_count'] : null,
            successTaskCount: isset($data['success_task_count']) ? (int) $data['success_task_count'] : null,
            failTaskCount: isset($data['fail_task_count']) ? (int) $data['fail_task_count'] : null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            submittedAt: isset($data['submitted_at'])
                ? CarbonImmutable::parse($data['submitted_at'])
                : null,
            closedAt: isset($data['closed_at'])
                ? CarbonImmutable::parse($data['closed_at'])
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
            'batch_type' => $this->batchType->value,
            'status' => $this->status->value,
            'version' => $this->version,
            'total_task_count' => $this->totalTaskCount,
            'success_task_count' => $this->successTaskCount,
            'fail_task_count' => $this->failTaskCount,
            'created_at' => $this->createdAt?->toIso8601String(),
            'submitted_at' => $this->submittedAt?->toIso8601String(),
            'closed_at' => $this->closedAt?->toIso8601String(),
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
     * Check if batch is in a terminal state
     *
     * @return bool True if batch is completed or failed
     */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Check if batch is processing
     *
     * @return bool True if batch is currently processing
     */
    public function isProcessing(): bool
    {
        return $this->status->isProcessing();
    }
}
