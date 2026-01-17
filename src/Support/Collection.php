<?php

declare(strict_types=1);

namespace Recharge\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Immutable Type-Safe Collection
 *
 * Generic collection class for handling arrays of DTOs or other objects.
 * Provides type safety, immutability, and common collection operations.
 *
 * @template T
 * @implements IteratorAggregate<int, T>
 * @implements ArrayAccess<int, T>
 */
final class Collection implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<int, T>
     */
    private array $items;

    /**
     * @param array<int, T> $items Collection items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values($items); // Re-index
    }

    /**
     * Get all items
     *
     * @return array<int, T>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get first item
     *
     * @return T|null
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get last item
     *
     * @return T|null
     */
    public function last(): mixed
    {
        if ($this->items === []) {
            return null;
        }

        return $this->items[array_key_last($this->items)];
    }

    /**
     * Check if collection is empty
     */
    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Check if collection is not empty
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Map over items
     *
     * @template U
     * @param callable(T, int): U $callback
     * @return Collection<U>
     */
    public function map(callable $callback): self
    {
        $items = [];
        foreach ($this->items as $key => $value) {
            $items[] = $callback($value, $key);
        }

        return new self($items);
    }

    /**
     * Filter items
     *
     * @param callable(T, int): bool $callback
     * @return Collection<T>
     */
    public function filter(callable $callback): self
    {
        $items = [];
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                $items[] = $value;
            }
        }

        return new self($items);
    }

    /**
     * Find first item matching predicate
     *
     * @param callable(T, int): bool $callback
     * @return T|null
     */
    public function find(callable $callback): mixed
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Check if any item matches predicate
     *
     * @param callable(T, int): bool $callback
     */
    public function contains(callable $callback): bool
    {
        return $this->find($callback) !== null;
    }

    /**
     * Chunk collection into smaller collections
     *
     * @param int $size Chunk size
     * @return Collection<Collection<T>>
     */
    public function chunk(int $size): self
    {
        $chunks = array_chunk($this->items, max(1, $size));
        $collections = array_map(fn ($chunk): \Recharge\Support\Collection => new self($chunk), $chunks);

        return new self($collections);
    }

    /**
     * Take first N items
     *
     * @param int $limit Number of items to take
     * @return Collection<T>
     */
    public function take(int $limit): self
    {
        return new self(array_slice($this->items, 0, $limit));
    }

    /**
     * Skip first N items
     *
     * @param int $offset Number of items to skip
     * @return Collection<T>
     */
    public function skip(int $offset): self
    {
        return new self(array_slice($this->items, $offset));
    }

    /**
     * Reduce collection to single value
     *
     * @template U
     * @param callable(U, T): U $callback
     * @param U $initial Initial value
     * @return U
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $carry = $initial;
        foreach ($this->items as $item) {
            $carry = $callback($carry, $item);
        }

        return $carry;
    }

    /**
     * Convert to JSON
     *
     * @param int $options JSON encode options
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->items, JSON_THROW_ON_ERROR | $options);
    }

    // IteratorAggregate implementation

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }

    // Countable implementation

    public function count(): int
    {
        return count($this->items);
    }

    // ArrayAccess implementation

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Collection is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('Collection is immutable');
    }
}
