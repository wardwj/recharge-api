<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;
use Recharge\Enums\CollectionSortOrder;

/**
 * Collection Data Transfer Object
 *
 * Immutable DTO representing a Recharge collection.
 * Collections are only available in API version 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/collections#the-collection-object
 */
final readonly class Collection implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Collection
     * @param string|null $title Collection title
     * @param string|null $description Collection description
     * @param string|null $type Collection type (typically 'manual')
     * @param CollectionSortOrder|null $sortOrder Sort order for products in collection
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $type = null,
        public ?CollectionSortOrder $sortOrder = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create Collection from API response array
     *
     * @param array<string, mixed> $data API response data
     * @return static Collection instance
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            type: $data['type'] ?? null,
            sortOrder: isset($data['sort_order']) ? CollectionSortOrder::tryFromString($data['sort_order']) : null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
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
        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'sort_order' => $this->sortOrder?->value,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value) => $value !== null);
    }

    /**
     * Get raw API response data
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
