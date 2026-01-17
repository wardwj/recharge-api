<?php

declare(strict_types=1);

namespace Recharge\Support;

use IteratorAggregate;
use Recharge\Enums\ApiVersion;
use Recharge\Http\Response;
use Recharge\RechargeClient;
use Traversable;

/**
 * Paginator - Version-aware automatic pagination
 *
 * Handles both API versions transparently:
 * - 2021-01: Reads cursors from Link HTTP headers
 * - 2021-11: Reads cursors from JSON response body
 *
 * Just iterate - we handle the rest!
 *
 * @template T
 * @implements IteratorAggregate<int, T>
 */
final class Paginator implements IteratorAggregate
{
    private ?string $nextCursor = null;

    private bool $hasMore = true;

    /**
     * @param RechargeClient $client The Recharge API client
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $queryParams Query parameters
     * @param callable(array<string, mixed>): T $mapper Function to map API response to DTO
     * @param string $itemsKey Key in response array containing items (e.g., 'subscriptions', 'customers')
     */
    public function __construct(
        private readonly RechargeClient $client,
        private readonly string $endpoint,
        private readonly array $queryParams = [],
        private readonly mixed $mapper = null,
        private readonly string $itemsKey = 'subscriptions'
    ) {
    }

    /**
     * Get iterator for automatic pagination
     *
     * Usage:
     *   foreach ($paginator as $item) { ... }
     *
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        $params = $this->queryParams;
        $this->hasMore = true;

        while ($this->hasMore) {
            // Get response with headers for version-aware cursor extraction
            $rawResponse = $this->client->getConnector()->get($this->endpoint, $params, includeHeaders: true);

            if (!($rawResponse instanceof Response)) {
                break;
            }

            $response = $rawResponse;

            // Extract items
            $items = $response->body[$this->itemsKey] ?? [];

            foreach ($items as $item) {
                if ($this->mapper !== null) {
                    yield ($this->mapper)($item);
                } else {
                    yield $item;
                }
            }

            // Extract cursors based on API version
            $cursors = $this->extractCursors($response);
            $this->nextCursor = $cursors['next'];

            if ($this->nextCursor) {
                $params['cursor'] = $this->nextCursor;
            } else {
                $this->hasMore = false;
            }
        }
    }

    /**
     * Extract cursors from response (version-aware)
     *
     * @return array{next: string|null, previous: string|null}
     */
    private function extractCursors(Response $response): array
    {
        $version = $this->client->getApiVersion();

        // API 2021-01: Cursors in Link header
        if ($version === ApiVersion::V2021_01) {
            $cursors = $response->extractCursorsFromLinkHeader();

            // Fallback to body if headers not present (some endpoints)
            if (!$cursors['next'] && !$cursors['previous']) {
                return $response->extractCursorsFromBody();
            }

            return $cursors;
        }

        // API 2021-11+: Cursors in response body
        return $response->extractCursorsFromBody();
    }

    /**
     * Get all items (careful with large datasets!)
     *
     * @return array<int, T>
     */
    public function all(): array
    {
        return iterator_to_array($this->getIterator(), false);
    }

    /**
     * Get first item
     *
     * @return T|null
     */
    public function first(): mixed
    {
        foreach ($this->getIterator() as $item) {
            return $item;
        }

        return null;
    }

    /**
     * Take first N items
     *
     * @param int $limit Number of items to take
     * @return array<int, T>
     */
    public function take(int $limit): array
    {
        $items = [];
        $count = 0;

        foreach ($this->getIterator() as $item) {
            $items[] = $item;
            if (++$count >= $limit) {
                break;
            }
        }

        return $items;
    }

    /**
     * Get items in chunks (memory efficient for large datasets)
     *
     * @param int $size Chunk size
     * @return \Generator<int, array<int, T>>
     */
    public function chunk(int $size): \Generator
    {
        $chunk = [];

        foreach ($this->getIterator() as $item) {
            $chunk[] = $item;

            if (count($chunk) >= $size) {
                yield $chunk;
                $chunk = [];
            }
        }

        if ($chunk !== []) {
            yield $chunk;
        }
    }

    /**
     * Count items (fetches all pages - use carefully!)
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Check if paginator has any items
     */
    public function isEmpty(): bool
    {
        return $this->first() === null;
    }
}
