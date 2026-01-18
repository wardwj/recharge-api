<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;

/**
 * Product Data Transfer Object
 *
 * Immutable DTO representing a Recharge product.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-11: external_product_id (used as identifier), vendor, description, published_at,
 *            requires_shipping, images, options, variants
 * - 2021-01: id (numeric), shopify_product_id, subscription_defaults, discount_amount, discount_type
 *
 * Note: Products in API version 2021-01 are deprecated as of June 30, 2025.
 * The recommended replacement is using Plans in 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/products#the-product-object
 * @see https://developer.rechargepayments.com/2021-01/products#the-product-object
 */
final readonly class Product implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Product (2021-01)
     * @param string|null $externalProductId External product ID (2021-11, may be used as identifier)
     * @param string|null $title Product title
     * @param string|null $handle Product handle
     * @param string|null $vendor Product vendor/brand
     * @param string|null $description Product description
     * @param bool|null $requiresShipping Whether product requires shipping
     * @param CarbonImmutable|null $publishedAt Published timestamp
     * @param int|null $shopifyProductId Shopify product ID (2021-01)
     * @param array<string, mixed>|null $images Product images array
     * @param array<string, mixed>|null $options Product options array
     * @param array<string, mixed>|null $variants Product variants array
     * @param array<string, mixed>|null $subscriptionDefaults Subscription defaults (2021-01)
     * @param string|float|null $discountAmount Discount amount (2021-01)
     * @param string|null $discountType Discount type (2021-01)
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $externalProductId = null,
        public ?string $title = null,
        public ?string $handle = null,
        public ?string $vendor = null,
        public ?string $description = null,
        public ?bool $requiresShipping = null,
        public ?CarbonImmutable $publishedAt = null,
        public ?int $shopifyProductId = null,
        public ?array $images = null,
        public ?array $options = null,
        public ?array $variants = null,
        public ?array $subscriptionDefaults = null,
        public string|float|null $discountAmount = null,
        public ?string $discountType = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     * Fields that exist in one version but not the other are handled gracefully.
     *
     * @param array<string, mixed> $data Product data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : 0,
            externalProductId: $data['external_product_id'] ?? null,
            title: $data['title'] ?? null,
            handle: $data['handle'] ?? null,
            vendor: $data['vendor'] ?? null,
            description: $data['description'] ?? null,
            requiresShipping: isset($data['requires_shipping']) ? (bool) $data['requires_shipping'] : null,
            publishedAt: isset($data['published_at'])
                ? CarbonImmutable::parse($data['published_at'])
                : null,
            shopifyProductId: isset($data['shopify_product_id']) ? (int) $data['shopify_product_id'] : null,
            images: $data['images'] ?? null,
            options: $data['options'] ?? null,
            variants: $data['variants'] ?? null,
            subscriptionDefaults: $data['subscription_defaults'] ?? null,
            discountAmount: isset($data['discount_amount']) ? (is_numeric($data['discount_amount']) ? (string) $data['discount_amount'] : $data['discount_amount']) : null,
            discountType: $data['discount_type'] ?? null,
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
     * Get the product identifier (works with both versions)
     *
     * In 2021-11, external_product_id is preferred; in 2021-01, id is used.
     *
     * @return int|string Product identifier
     */
    public function getIdentifier(): int|string
    {
        return $this->externalProductId ?? $this->id;
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
            'external_product_id' => $this->externalProductId,
            'title' => $this->title,
            'handle' => $this->handle,
            'vendor' => $this->vendor,
            'description' => $this->description,
            'requires_shipping' => $this->requiresShipping,
            'published_at' => $this->publishedAt?->toIso8601String(),
            'shopify_product_id' => $this->shopifyProductId,
            'images' => $this->images,
            'options' => $this->options,
            'variants' => $this->variants,
            'subscription_defaults' => $this->subscriptionDefaults,
            'discount_amount' => $this->discountAmount,
            'discount_type' => $this->discountType,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
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
