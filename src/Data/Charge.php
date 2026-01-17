<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Enums\ChargeStatus;

/**
 * Charge Data Transfer Object
 *
 * Immutable DTO representing a Recharge charge.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-11: billing_address_country_code, shipping_address_country_code, orders_count
 *
 * @see https://developer.rechargepayments.com/2021-11/charges#the-charge-object
 * @see https://developer.rechargepayments.com/2021-01/charges#the-charge-object
 */
final readonly class Charge
{
    /**
     * @param int $id Unique numeric identifier for the Charge
     * @param int $customerId Unique numeric identifier of the customer
     * @param int|null $addressId Address ID
     * @param int|null $subscriptionId Unique numeric identifier of the subscription
     * @param string|null $subtotalPrice Subtotal price
     * @param string|null $totalPrice Total price including tax
     * @param ChargeStatus|null $status Status of the charge
     * @param string|null $billingAddressCountryCode Billing country code (2021-11+)
     * @param string|null $shippingAddressCountryCode Shipping country code (2021-11+)
     * @param int|null $ordersCount Number of orders (2021-11+)
     * @param CarbonImmutable|null $scheduledAt Scheduled charge date
     * @param CarbonImmutable|null $processedAt Processed timestamp
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public ?int $addressId = null,
        public ?int $subscriptionId = null,
        public ?string $subtotalPrice = null,
        public ?string $totalPrice = null,
        public ?ChargeStatus $status = null,
        public ?string $note = null,
        public ?string $tags = null,
        public ?string $error = null,
        public ?string $errorType = null,
        public ?string $billingAddressCountryCode = null,
        public ?string $shippingAddressCountryCode = null,
        public ?int $ordersCount = null,
        public ?CarbonImmutable $scheduledAt = null,
        public ?CarbonImmutable $processedAt = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Charge data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            addressId: isset($data['address_id']) ? (int) $data['address_id'] : null,
            subscriptionId: isset($data['subscription_id']) ? (int) $data['subscription_id'] : null,
            subtotalPrice: $data['subtotal_price'] ?? null,
            totalPrice: $data['total_price'] ?? null,
            status: isset($data['status']) ? ChargeStatus::tryFromString($data['status']) : null,
            note: $data['note'] ?? null,
            tags: isset($data['tags']) ? (is_string($data['tags']) ? $data['tags'] : (string) json_encode($data['tags'])) : null,
            error: isset($data['error']) ? (is_string($data['error']) ? $data['error'] : (string) json_encode($data['error'])) : null,
            errorType: $data['error_type'] ?? null,
            billingAddressCountryCode: $data['billing_address_country_code'] ?? null,
            shippingAddressCountryCode: $data['shipping_address_country_code'] ?? null,
            ordersCount: isset($data['orders_count']) ? (int) $data['orders_count'] : null,
            scheduledAt: isset($data['scheduled_at'])
                ? CarbonImmutable::parse($data['scheduled_at'])
                : null,
            processedAt: isset($data['processed_at'])
                ? CarbonImmutable::parse($data['processed_at'])
                : null,
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
            'customer_id' => $this->customerId,
            'address_id' => $this->addressId,
            'subscription_id' => $this->subscriptionId,
            'subtotal_price' => $this->subtotalPrice,
            'total_price' => $this->totalPrice,
            'status' => $this->status?->value,
            'billing_address_country_code' => $this->billingAddressCountryCode,
            'shipping_address_country_code' => $this->shippingAddressCountryCode,
            'orders_count' => $this->ordersCount,
            'scheduled_at' => $this->scheduledAt?->toIso8601String(),
            'processed_at' => $this->processedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
