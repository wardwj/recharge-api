<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Bundle Selection Data Transfer Object
 *
 * Immutable DTO representing a Recharge bundle selection.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/bundle_selections#the-bundle-selection-object
 * @see https://developer.rechargepayments.com/2021-01/bundle_selections#the-bundle-selection-object
 */
final readonly class Bundle
{
    /**
     * @param int $id Unique numeric identifier for the Bundle Selection
     * @param int|null $bundleVariantId Bundle variant ID
     * @param int|null $purchaseItemId Purchase item ID
     * @param array<string, mixed>|null $externalProductId External product ID object
     * @param array<string, mixed>|null $externalVariantId External variant ID object
     * @param array<string, mixed>|null $items Array of bundle selection items
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?int $bundleVariantId = null,
        public ?int $purchaseItemId = null,
        public ?array $externalProductId = null,
        public ?array $externalVariantId = null,
        public ?array $items = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create Bundle Selection from API response array
     *
     * @param array<string, mixed> $data API response data
     * @return static Bundle instance
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            bundleVariantId: isset($data['bundle_variant_id']) ? (int) $data['bundle_variant_id'] : null,
            purchaseItemId: isset($data['purchase_item_id']) ? (int) $data['purchase_item_id'] : null,
            externalProductId: $data['external_product_id'] ?? null,
            externalVariantId: $data['external_variant_id'] ?? null,
            items: $data['items'] ?? null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            rawData: $data
        );
    }
}
