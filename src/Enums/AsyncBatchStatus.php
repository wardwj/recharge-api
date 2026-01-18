<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Async Batch Status enumeration
 *
 * Represents the status of an async batch operation.
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints#the-async-batch-object
 * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints#the-async-batch-object
 */
enum AsyncBatchStatus: string
{
    case NOT_STARTED = 'not_started';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    /**
     * Check if the batch is in a terminal state (completed or failed)
     *
     * @return bool True if batch is completed or failed
     */
    public function isTerminal(): bool
    {
        return $this === self::COMPLETED || $this === self::FAILED;
    }

    /**
     * Check if the batch is in progress
     *
     * @return bool True if batch is processing
     */
    public function isProcessing(): bool
    {
        return $this === self::PROCESSING;
    }

    /**
     * Check if the batch has started
     *
     * @return bool True if batch is processing, completed, or failed
     */
    public function hasStarted(): bool
    {
        return $this !== self::NOT_STARTED;
    }

    /**
     * Try to create from string value
     *
     * @param string $value Status value (e.g., 'completed')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
