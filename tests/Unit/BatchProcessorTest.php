<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Support\BatchProcessor;

/**
 * Unit tests for BatchProcessor
 */
class BatchProcessorTest extends TestCase
{
    public function testBatchProcessorCreation(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 100);

        $this->assertInstanceOf(BatchProcessor::class, $processor);
    }

    public function testBatchProcessorProcessesAllItems(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $result = $processor->process(
            items: $items,
            processor: fn ($item): int => $item * 2
        );

        $this->assertEquals(5, $result->total());
        $this->assertEquals(5, $result->successful);
        $this->assertEquals(0, $result->failed);
        $this->assertEquals([2, 4, 6, 8, 10], $result->results);
    }

    public function testBatchProcessorHandlesErrors(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $result = $processor->process(
            items: $items,
            processor: function ($item): int {
                if ($item === 3) {
                    throw new \Exception('Error on item 3');
                }

                return $item * 2;
            }
        );

        $this->assertEquals(5, $result->total());
        $this->assertEquals(4, $result->successful);
        $this->assertEquals(1, $result->failed);
        $this->assertCount(1, $result->errors);
    }

    public function testBatchProcessorCallsErrorHandler(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $erroredItems = [];
        $items = [1, 2, 3, 4, 5];

        $result = $processor->process(
            items: $items,
            processor: function ($item): int {
                if ($item === 3) {
                    throw new \Exception('Error');
                }

                return $item * 2;
            },
            errorHandler: function ($item, $exception) use (&$erroredItems): void {
                $erroredItems[] = $item;
            }
        );

        $this->assertEquals([3], $erroredItems);
        $this->assertTrue($result->hasErrors());
    }

    public function testBatchResultSuccessRate(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $result = $processor->process(
            items: $items,
            processor: function ($item): int {
                if ($item === 3 || $item === 4) {
                    throw new \Exception('Error');
                }

                return $item * 2;
            }
        );

        $this->assertEquals(60.0, $result->successRate());
        $this->assertEquals(3, $result->successful);
        $this->assertEquals(2, $result->failed);
    }

    public function testBatchResultIsFullSuccess(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 0);

        $items = [1, 2, 3];
        $result = $processor->process(
            items: $items,
            processor: fn ($item): int => $item * 2
        );

        $this->assertTrue($result->isFullSuccess());
        $this->assertFalse($result->hasErrors());
    }

    public function testBatchResultHasSuccesses(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 0);

        $items = [1, 2, 3];
        $result = $processor->process(
            items: $items,
            processor: function ($item): int {
                if ($item === 2) {
                    throw new \Exception('Error');
                }

                return $item * 2;
            }
        );

        $this->assertTrue($result->hasSuccesses());
        $this->assertTrue($result->hasErrors());
        $this->assertFalse($result->isFullSuccess());
    }

    public function testBatchResultGetErrorMessages(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 0);

        $items = [1, 2, 3];
        $result = $processor->process(
            items: $items,
            processor: function ($item): int {
                if ($item === 2) {
                    throw new \Exception('Error on item 2');
                }

                return $item * 2;
            }
        );

        $messages = $result->getErrorMessages();
        $this->assertCount(1, $messages);
        $this->assertEquals('Error on item 2', $messages[0]);
    }

    public function testBatchResultToArray(): void
    {
        $processor = new BatchProcessor(chunkSize: 10, delayMs: 0);

        $items = [1, 2, 3];
        $result = $processor->process(
            items: $items,
            processor: fn ($item): int => $item * 2
        );

        $array = $result->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('total', $array);
        $this->assertArrayHasKey('successful', $array);
        $this->assertArrayHasKey('failed', $array);
        $this->assertArrayHasKey('success_rate', $array);
        $this->assertEquals(3, $array['total']);
        $this->assertEquals('100%', $array['success_rate']);
    }

    public function testBatchProcessorMap(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $results = $processor->map($items, fn ($item): int => $item * 2);

        $this->assertEquals([2, 4, 6, 8, 10], $results);
    }

    public function testBatchProcessorAsync(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $results = [];

        foreach ($processor->processAsync($items, fn ($item): int => $item * 2) as $result) {
            if (is_array($result) && isset($result['error'])) {
                // Error case
                continue;
            }
            $results[] = $result;
        }

        $this->assertEquals([2, 4, 6, 8, 10], $results);
    }

    public function testBatchProcessorAsyncHandlesErrors(): void
    {
        $processor = new BatchProcessor(chunkSize: 2, delayMs: 0);

        $items = [1, 2, 3, 4, 5];
        $results = [];
        $errors = [];

        foreach ($processor->processAsync($items, function ($item): int {
            if ($item === 3) {
                throw new \Exception('Error on 3');
            }

            return $item * 2;
        }) as $result) {
            if (is_array($result) && isset($result['error'])) {
                $errors[] = $result;
            } else {
                $results[] = $result;
            }
        }

        $this->assertCount(4, $results);
        $this->assertCount(1, $errors);
    }
}
