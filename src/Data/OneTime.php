<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;

/**
 * OneTime Data Transfer Object
 *
 * Immutable DTO representing a Recharge one-time purchase item.
 * Handles both API versions 2021-01 and 2021-11.
 * One-times are non-recurring line items attached to a QUEUED charge.
 *
 * @see https://developer.rechargepayments.com/2021-11/onetimes#the-onetime-object
 * @see https://developer.rechargepayments.com/2021-01/onetimes#the-onetime-object
 */
final readonly class OneTime implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the OneTime
     * @param int $addressId Address ID for shipping
     * @param int|null $customerId Customer ID
     * @param int|null $chargeId Charge ID (QUEUED charge)
     * @param string|null $externalVariantId External variant ID
     * @param int|null $quantity Quantity of items
     * @param string|float|null $price Price as string or float
     * @param string|null $title Product title
     * @param string|null $variantTitle Variant title
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $addressId,
        public ?int $customerId = null,
        public ?int $chargeId = null,
        public ?string $externalVariantId = null,
        public ?int $quantity = null,
        public string|float|null $price = null,
        public ?string $title = null,
        public ?string $variantTitle = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     *
     * @param array<string, mixed> $data OneTime data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            addressId: (int) ($data['address_id'] ?? 0),
            customerId: isset($data['customer_id']) ? (int) $data['customer_id'] : null,
            chargeId: isset($data['charge_id']) ? (int) $data['charge_id'] : null,
            externalVariantId: $data['external_variant_id'] ?? null,
            quantity: isset($data['quantity']) ? (int) $data['quantity'] : null,
            price: isset($data['price']) ? (is_numeric($data['price']) ? (string) $data['price'] : $data['price']) : null,
            title: $data['title'] ?? null,
            variantTitle: $data['variant_title'] ?? null,
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
            'address_id' => $this->addressId,
            'customer_id' => $this->customerId,
            'charge_id' => $this->chargeId,
            'external_variant_id' => $this->externalVariantId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'title' => $this->title,
            'variant_title' => $this->variantTitle,
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
