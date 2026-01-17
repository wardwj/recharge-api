<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Enums\OrderStatus;

/**
 * Order Data Transfer Object
 *
 * Immutable DTO representing a Recharge order.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-11: billing_address_country_code, shipping_address_country_code
 *
 * @see https://developer.rechargepayments.com/2021-11/orders#the-order-object
 * @see https://developer.rechargepayments.com/2021-01/orders#the-order-object
 */
final readonly class Order
{
    /**
     * @param int $id Unique numeric identifier for the Order
     * @param int $customerId Unique numeric identifier of the customer
     * @param int|null $addressId Address ID
     * @param int|null $chargeId Charge ID
     * @param string|null $orderNumber Order number
     * @param string|null $totalPrice Total price
     * @param string|null $financialStatus Financial status
     * @param OrderStatus|null $status Order status
     * @param string|null $billingAddressCountryCode Billing country code (2021-11+)
     * @param string|null $shippingAddressCountryCode Shipping country code (2021-11+)
     * @param CarbonImmutable|null $scheduledAt Scheduled date
     * @param CarbonImmutable|null $processedAt Processed timestamp
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public ?int $addressId = null,
        public ?int $chargeId = null,
        public ?string $orderNumber = null,
        public string|float|null $totalPrice = null,
        public ?string $financialStatus = null,
        public ?OrderStatus $status = null,
        public ?string $error = null,
        public ?string $note = null,
        public ?string $tags = null,
        public ?string $externalOrderId = null,
        public ?string $billingAddressCountryCode = null,
        public ?string $shippingAddressCountryCode = null,
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
     * @param array<string, mixed> $data Order data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            addressId: isset($data['address_id']) ? (int) $data['address_id'] : null,
            chargeId: isset($data['charge_id']) ? (int) $data['charge_id'] : null,
            orderNumber: $data['order_number'] ?? null,
            totalPrice: isset($data['total_price']) ? (is_numeric($data['total_price']) ? (string) $data['total_price'] : $data['total_price']) : null,
            financialStatus: $data['financial_status'] ?? null,
            status: isset($data['status']) ? OrderStatus::tryFromString($data['status']) : null,
            billingAddressCountryCode: $data['billing_address_country_code'] ?? null,
            shippingAddressCountryCode: $data['shipping_address_country_code'] ?? null,
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
            'charge_id' => $this->chargeId,
            'order_number' => $this->orderNumber,
            'total_price' => $this->totalPrice,
            'financial_status' => $this->financialStatus,
            'status' => $this->status?->value,
            'billing_address_country_code' => $this->billingAddressCountryCode,
            'shipping_address_country_code' => $this->shippingAddressCountryCode,
            'scheduled_at' => $this->scheduledAt?->toIso8601String(),
            'processed_at' => $this->processedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
