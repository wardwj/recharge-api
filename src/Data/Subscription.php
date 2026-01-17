<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;
use Recharge\Enums\IntervalUnit;
use Recharge\Enums\SubscriptionStatus;

/**
 * Subscription Data Transfer Object
 *
 * Immutable DTO representing a Recharge subscription.
 * Handles both API versions 2021-01 and 2021-11.
 * Fields that exist in one version but not the other are nullable.
 *
 * Version-Specific Fields:
 * - 2021-11: plan_id, external_product_id, external_variant_id, presentment_currency,
 *            is_prepaid, is_skippable, is_swappable, analytics_data, title (replaces product_title)
 * - 2021-01: product_title, shipping_date (deprecated in 2021-11)
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#the-subscription-object
 * @see https://developer.rechargepayments.com/2021-01/subscriptions#the-subscription-object
 */
final readonly class Subscription implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Subscription
     * @param int $customerId Unique numeric identifier of the customer
     * @param int|null $addressId Address ID for shipping
     * @param string|null $productTitle Product title (deprecated in 2021-11, use title)
     * @param string|null $title Product title (2021-11+, replaces product_title)
     * @param string|null $variantTitle Variant title
     * @param int|null $quantity Quantity of items
     * @param string|null $price Price as string
     * @param SubscriptionStatus|null $status Subscription status
     * @param IntervalUnit|null $orderIntervalUnit Order interval unit
     * @param int|null $orderIntervalFrequency Order interval frequency
     * @param int|null $orderDayOfMonth Order day of month (1-31)
     * @param int|null $orderDayOfWeek Order day of week (0-6, where 0 is Sunday)
     * @param int|null $chargeIntervalFrequency Charge interval frequency
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param CarbonImmutable|null $nextChargeScheduledAt Next charge scheduled date (2021-11: scheduled_at)
     * @param CarbonImmutable|null $shippingDate Shipping date (2021-01, deprecated)
     * @param CarbonImmutable|null $cancelledAt Cancelled timestamp
     * @param string|null $cancellationReason Reason for cancellation
     * @param string|null $cancellationReasonComments Additional cancellation comments
     * @param int|null $planId Plan ID (2021-11+)
     * @param array<string, mixed>|null $externalProductId External product ID object (2021-11+)
     * @param array<string, mixed>|null $externalVariantId External variant ID object (2021-11+)
     * @param string|null $presentmentCurrency Currency code (2021-11+)
     * @param bool|null $isPrepaid Is prepaid subscription (2021-11+)
     * @param bool|null $isSkippable Can be skipped (2021-11+)
     * @param bool|null $isSwappable Can products be swapped (2021-11+)
     * @param int|null $shopifyVariantId Shopify variant ID (2021-01)
     * @param string|null $shopifyId Shopify ID (2021-01, deprecated)
     * @param int|null $shopifyOrderId Shopify order ID (2021-11)
     * @param int|null $expireAfterSpecificNumberOfCharges Number of charges before expiry
     * @param array<string, mixed>|null $analyticsData Analytics data (2021-11+)
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public ?int $addressId = null,
        public ?string $productTitle = null,
        public ?string $title = null,
        public ?string $variantTitle = null,
        public ?int $quantity = null,
        public string|float|null $price = null,
        public ?SubscriptionStatus $status = null,
        public ?IntervalUnit $orderIntervalUnit = null,
        public ?int $orderIntervalFrequency = null,
        public ?int $orderDayOfMonth = null,
        public ?int $orderDayOfWeek = null,
        public ?int $chargeIntervalFrequency = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public ?CarbonImmutable $nextChargeScheduledAt = null,
        public ?CarbonImmutable $shippingDate = null,
        public ?CarbonImmutable $cancelledAt = null,
        public ?string $cancellationReason = null,
        public ?string $cancellationReasonComments = null,
        public ?int $planId = null,
        public ?array $externalProductId = null,
        public ?array $externalVariantId = null,
        public ?string $presentmentCurrency = null,
        public ?bool $isPrepaid = null,
        public ?bool $isSkippable = null,
        public ?bool $isSwappable = null,
        public ?int $shopifyVariantId = null,
        public ?string $shopifyId = null,
        public ?int $shopifyOrderId = null,
        public ?int $expireAfterSpecificNumberOfCharges = null,
        public ?array $analyticsData = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     * Fields that exist in one version but not the other are handled gracefully.
     *
     * @param array<string, mixed> $data Subscription data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            addressId: isset($data['address_id']) ? (int) $data['address_id'] : null,
            productTitle: $data['product_title'] ?? null,
            title: $data['title'] ?? null,
            variantTitle: $data['variant_title'] ?? null,
            quantity: isset($data['quantity']) ? (int) $data['quantity'] : null,
            price: isset($data['price']) ? (is_numeric($data['price']) ? (string) $data['price'] : $data['price']) : null,
            status: isset($data['status']) ? SubscriptionStatus::tryFromString($data['status']) : null,
            orderIntervalUnit: isset($data['order_interval_unit'])
                ? IntervalUnit::tryFrom($data['order_interval_unit'])
                : null,
            orderIntervalFrequency: isset($data['order_interval_frequency'])
                ? (int) $data['order_interval_frequency']
                : null,
            orderDayOfMonth: isset($data['order_day_of_month'])
                ? (int) $data['order_day_of_month']
                : null,
            orderDayOfWeek: isset($data['order_day_of_week'])
                ? (int) $data['order_day_of_week']
                : null,
            chargeIntervalFrequency: isset($data['charge_interval_frequency'])
                ? (int) $data['charge_interval_frequency']
                : null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            nextChargeScheduledAt: isset($data['next_charge_scheduled_at'])
                ? CarbonImmutable::parse($data['next_charge_scheduled_at'])
                : (isset($data['scheduled_at']) ? CarbonImmutable::parse($data['scheduled_at']) : null),
            shippingDate: isset($data['shipping_date'])
                ? CarbonImmutable::parse($data['shipping_date'])
                : null,
            cancelledAt: isset($data['cancelled_at'])
                ? CarbonImmutable::parse($data['cancelled_at'])
                : null,
            cancellationReason: $data['cancellation_reason'] ?? null,
            cancellationReasonComments: $data['cancellation_reason_comments'] ?? null,
            planId: isset($data['plan_id']) ? (int) $data['plan_id'] : null,
            externalProductId: $data['external_product_id'] ?? null,
            externalVariantId: $data['external_variant_id'] ?? null,
            presentmentCurrency: $data['presentment_currency'] ?? null,
            isPrepaid: isset($data['is_prepaid']) ? (bool) $data['is_prepaid'] : null,
            isSkippable: isset($data['is_skippable']) ? (bool) $data['is_skippable'] : null,
            isSwappable: isset($data['is_swappable']) ? (bool) $data['is_swappable'] : null,
            shopifyVariantId: isset($data['shopify_variant_id']) ? (int) $data['shopify_variant_id'] : null,
            shopifyId: $data['shopify_id'] ?? null,
            shopifyOrderId: isset($data['shopify_order_id']) ? (int) $data['shopify_order_id'] : null,
            expireAfterSpecificNumberOfCharges: isset($data['expire_after_specific_number_of_charges'])
                ? (int) $data['expire_after_specific_number_of_charges']
                : null,
            analyticsData: $data['analytics_data'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get canonical product title (works with both versions)
     */
    public function getProductTitle(): ?string
    {
        return $this->title ?? $this->productTitle;
    }

    /**
     * Get scheduled date (works with both versions)
     */
    public function getScheduledAt(): ?CarbonImmutable
    {
        return $this->nextChargeScheduledAt ?? $this->shippingDate;
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
            'product_title' => $this->productTitle,
            'title' => $this->title,
            'variant_title' => $this->variantTitle,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'status' => $this->status?->value,
            'order_interval_unit' => $this->orderIntervalUnit?->value,
            'order_interval_frequency' => $this->orderIntervalFrequency,
            'order_day_of_month' => $this->orderDayOfMonth,
            'order_day_of_week' => $this->orderDayOfWeek,
            'charge_interval_frequency' => $this->chargeIntervalFrequency,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'next_charge_scheduled_at' => $this->nextChargeScheduledAt?->toIso8601String(),
            'shipping_date' => $this->shippingDate?->toIso8601String(),
            'cancelled_at' => $this->cancelledAt?->toIso8601String(),
            'cancellation_reason' => $this->cancellationReason,
            'cancellation_reason_comments' => $this->cancellationReasonComments,
            'plan_id' => $this->planId,
            'external_product_id' => $this->externalProductId,
            'external_variant_id' => $this->externalVariantId,
            'presentment_currency' => $this->presentmentCurrency,
            'is_prepaid' => $this->isPrepaid,
            'is_skippable' => $this->isSkippable,
            'is_swappable' => $this->isSwappable,
            'shopify_variant_id' => $this->shopifyVariantId,
            'shopify_id' => $this->shopifyId,
            'shopify_order_id' => $this->shopifyOrderId,
            'expire_after_specific_number_of_charges' => $this->expireAfterSpecificNumberOfCharges,
            'analytics_data' => $this->analyticsData,
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
