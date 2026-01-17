<?php

declare(strict_types=1);

namespace Recharge\Support;

/**
 * Batch Processor
 *
 * Efficiently processes bulk operations with automatic chunking and error handling.
 * Useful for processing large datasets with rate limiting awareness.
 */
final readonly class BatchProcessor
{
    private const DEFAULT_CHUNK_SIZE = 50;
    private const DEFAULT_DELAY_MS = 100;

    /**
     * @param int $chunkSize Number of items per chunk
     * @param int $delayMs Delay between chunks in milliseconds
     */
    public function __construct(
        private int $chunkSize = self::DEFAULT_CHUNK_SIZE,
        private int $delayMs = self::DEFAULT_DELAY_MS
    ) {
    }

    /**
     * Process items in batches
     *
     * @template T
     * @template R
     * @param array<int, T> $items Items to process
     * @param callable(T): R $processor Processing function
     * @param callable(T, \Throwable): void|null $errorHandler Error handler
     * @return BatchResult<R> Batch processing result
     */
    public function process(
        array $items,
        callable $processor,
        ?callable $errorHandler = null
    ): BatchResult {
        $results = [];
        $errors = [];
        $chunks = array_chunk($items, max(1, $this->chunkSize));
        $totalChunks = count($chunks);

        foreach ($chunks as $chunkIndex => $chunk) {
            foreach ($chunk as $itemIndex => $item) {
                try {
                    $results[] = $processor($item);
                } catch (\Throwable $e) {
                    $errors[] = [
                        'item' => $item,
                        'error' => $e,
                        'chunk' => $chunkIndex,
                        'index' => $itemIndex,
                    ];

                    if ($errorHandler !== null) {
                        $errorHandler($item, $e);
                    }
                }
            }

            // Delay between chunks to respect rate limits
            if ($chunkIndex < $totalChunks - 1 && $this->delayMs > 0) {
                usleep($this->delayMs * 1000);
            }
        }

        return new BatchResult(
            successful: count($results),
            failed: count($errors),
            results: $results,
            errors: $errors
        );
    }

    /**
     * Process items in batches asynchronously (generator-based)
     *
     * Yields results as they become available instead of waiting for all.
     *
     * @template T
     * @template R
     * @param array<int, T> $items Items to process
     * @param callable(T): R $processor Processing function
     * @return \Generator<int, R|array{error: \Throwable, item: T}>
     */
    public function processAsync(array $items, callable $processor): \Generator
    {
        $chunks = array_chunk($items, max(1, $this->chunkSize));
        $totalChunks = count($chunks);

        foreach ($chunks as $chunkIndex => $chunk) {
            foreach ($chunk as $item) {
                try {
                    yield $processor($item);
                } catch (\Throwable $e) {
                    yield ['error' => $e, 'item' => $item];
                }
            }

            // Delay between chunks
            if ($chunkIndex < $totalChunks - 1 && $this->delayMs > 0) {
                usleep($this->delayMs * 1000);
            }
        }
    }

    /**
     * Map items with automatic batching
     *
     * @template T
     * @template R
     * @param array<int, T> $items Items to map
     * @param callable(T): R $mapper Mapping function
     * @return array<int, R> Mapped results (excluding errors)
     */
    public function map(array $items, callable $mapper): array
    {
        return $this->process($items, $mapper)->results;
    }
}

/**
 * Batch Processing Result
 *
 * @template T
 */
final readonly class BatchResult
{
    /**
     * @param int $successful Number of successful operations
     * @param int $failed Number of failed operations
     * @param array<int, T> $results Successful results
     * @param array<int, array{item: mixed, error: \Throwable, chunk: int, index: int}> $errors Errors with context
     */
    public function __construct(
        public int $successful,
        public int $failed,
        public array $results,
        public array $errors
    ) {
    }

    /**
     * Get total number of processed items
     */
    public function total(): int
    {
        return $this->successful + $this->failed;
    }

    /**
     * Get success rate as percentage
     */
    public function successRate(): float
    {
        $total = $this->total();

        return $total > 0 ? ($this->successful / $total) * 100 : 0.0;
    }

    /**
     * Check if all operations succeeded
     */
    public function isFullSuccess(): bool
    {
        return $this->failed === 0;
    }

    /**
     * Check if any operations succeeded
     */
    public function hasSuccesses(): bool
    {
        return $this->successful > 0;
    }

    /**
     * Check if any operations failed
     */
    public function hasErrors(): bool
    {
        return $this->failed > 0;
    }

    /**
     * Get error messages
     *
     * @return array<int, string>
     */
    public function getErrorMessages(): array
    {
        return array_map(
            fn ($error): string => $error['error']->getMessage(),
            $this->errors
        );
    }

    /**
     * Convert to array for logging
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total(),
            'successful' => $this->successful,
            'failed' => $this->failed,
            'success_rate' => round($this->successRate(), 2) . '%',
            'errors' => array_map(
                fn ($error): array => [
                    'message' => $error['error']->getMessage(),
                    'chunk' => $error['chunk'],
                    'index' => $error['index'],
                ],
                $this->errors
            ),
        ];
    }
}
