<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Product Data Transfer Object
 *
 * Immutable DTO representing a Recharge product.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/products#the-product-object
 * @see https://developer.rechargepayments.com/2021-01/products#the-product-object
 */
final readonly class Product
{
    /**
     * @param int $id Unique numeric identifier for the Product
     * @param string|null $title Product title
     * @param string|null $handle Product handle
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $title = null,
        public ?string $handle = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Product data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            title: $data['title'] ?? null,
            handle: $data['handle'] ?? null,
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'handle' => $this->handle,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ];
    }
}
