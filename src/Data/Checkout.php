<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;

/**
 * Checkout Data Transfer Object
 *
 * Immutable DTO representing a Recharge checkout.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Note: Checkouts are only available for BigCommerce and Custom setups.
 * Not supported for Shopify stores (deprecated as of October 18, 2024).
 *
 * @see https://developer.rechargepayments.com/2021-11/checkouts#the-checkout-object
 * @see https://developer.rechargepayments.com/2021-01/checkouts#the-checkout-object
 */
final readonly class Checkout implements DataTransferObjectInterface
{
    /**
     * @param string $token Unique checkout token identifier
     * @param string|null $email Customer email
     * @param array<string, mixed>|null $lineItems Array of line items
     * @param array<string, mixed>|null $billingAddress Billing address object
     * @param array<string, mixed>|null $shippingAddress Shipping address object
     * @param array<string, mixed>|null $appliedDiscounts Applied discounts array
     * @param array<string, mixed>|null $availableShippingRates Available shipping rates
     * @param array<string, mixed>|null $appliedShippingRate Applied shipping rate
     * @param string|null $currency Currency code
     * @param array<string, mixed>|null $analyticsData Analytics/UTM data
     * @param string|null $externalCheckoutId External checkout ID
     * @param string|null $externalCheckoutSource External checkout source
     * @param string|null $note Checkout note
     * @param array<string, mixed>|null $orderAttributes Order attributes
     * @param int|null $chargeId Charge ID (set after processing)
     * @param CarbonImmutable|null $completedAt Completed timestamp
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public string $token,
        public ?string $email = null,
        public ?array $lineItems = null,
        public ?array $billingAddress = null,
        public ?array $shippingAddress = null,
        public ?array $appliedDiscounts = null,
        public ?array $availableShippingRates = null,
        public ?array $appliedShippingRate = null,
        public ?string $currency = null,
        public ?array $analyticsData = null,
        public ?string $externalCheckoutId = null,
        public ?string $externalCheckoutSource = null,
        public ?string $note = null,
        public ?array $orderAttributes = null,
        public ?int $chargeId = null,
        public ?CarbonImmutable $completedAt = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create Checkout from API response array
     *
     * @param array<string, mixed> $data API response data
     * @return static Checkout instance
     */
    public static function fromArray(array $data): static
    {
        return new self(
            token: (string) ($data['token'] ?? ''),
            email: $data['email'] ?? null,
            lineItems: $data['line_items'] ?? $data['lineItems'] ?? null,
            billingAddress: $data['billing_address'] ?? $data['billingAddress'] ?? null,
            shippingAddress: $data['shipping_address'] ?? $data['shippingAddress'] ?? null,
            appliedDiscounts: $data['applied_discounts'] ?? $data['appliedDiscounts'] ?? $data['applied_discount'] ?? null,
            availableShippingRates: $data['available_shipping_rates'] ?? $data['availableShippingRates'] ?? null,
            appliedShippingRate: $data['applied_shipping_rate'] ?? $data['appliedShippingRate'] ?? null,
            currency: $data['currency'] ?? null,
            analyticsData: $data['analytics_data'] ?? $data['analyticsData'] ?? null,
            externalCheckoutId: $data['external_checkout_id'] ?? $data['externalCheckoutId'] ?? null,
            externalCheckoutSource: $data['external_checkout_source'] ?? $data['externalCheckoutSource'] ?? null,
            note: $data['note'] ?? null,
            orderAttributes: $data['order_attributes'] ?? $data['orderAttributes'] ?? null,
            chargeId: isset($data['charge_id']) ? (int) $data['charge_id'] : null,
            completedAt: isset($data['completed_at'])
                ? CarbonImmutable::parse($data['completed_at'])
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
            'token' => $this->token,
            'email' => $this->email,
            'line_items' => $this->lineItems,
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,
            'applied_discounts' => $this->appliedDiscounts,
            'available_shipping_rates' => $this->availableShippingRates,
            'applied_shipping_rate' => $this->appliedShippingRate,
            'currency' => $this->currency,
            'analytics_data' => $this->analyticsData,
            'external_checkout_id' => $this->externalCheckoutId,
            'external_checkout_source' => $this->externalCheckoutSource,
            'note' => $this->note,
            'order_attributes' => $this->orderAttributes,
            'charge_id' => $this->chargeId,
            'completed_at' => $this->completedAt?->toIso8601String(),
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
