<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Customer Data Transfer Object
 *
 * Immutable DTO representing a Recharge customer.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-11: tax_exempt, has_valid_payment_method, has_payment_method_in_dunning,
 *            external_customer_id, analytics_data, first_charge_processed_at,
 *            apply_credit_to_next_recurring_charge
 *
 * @see https://developer.rechargepayments.com/2021-11/customers#the-customer-object
 * @see https://developer.rechargepayments.com/2021-01/customers#the-customer-object
 */
final readonly class Customer
{
    /**
     * @param int $id Unique numeric identifier for the Customer
     * @param string|null $email Email address of the customer
     * @param string|null $firstName First name of the customer
     * @param string|null $lastName Last name of the customer
     * @param string|null $phone Phone number of the customer
     * @param string|null $hash Customer hash for authentication
     * @param int|null $subscriptionsActiveCount Active subscriptions count
     * @param int|null $subscriptionsTotalCount Total subscriptions count
     * @param bool|null $taxExempt Tax exempt status (2021-11+)
     * @param bool|null $hasValidPaymentMethod Has valid payment method (2021-11+)
     * @param bool|null $hasPaymentMethodInDunning Has payment method in dunning (2021-11+)
     * @param bool|null $applyCreditToNextRecurringCharge Apply credit to next charge (2021-11+)
     * @param array<string, mixed>|null $externalCustomerId External customer ID object (2021-11+)
     * @param array<string, mixed>|null $analyticsData Analytics data (2021-11+)
     * @param CarbonImmutable|null $firstChargeProcessedAt First charge processed timestamp (2021-11+)
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $email = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?string $billingAddress1 = null,
        public ?string $billingAddress2 = null,
        public ?string $billingCity = null,
        public ?string $billingProvince = null,
        public ?string $billingZip = null,
        public ?string $billingCountry = null,
        public ?string $billingCompany = null,
        public ?string $billingPhone = null,
        public ?string $hash = null,
        public ?int $subscriptionsActiveCount = null,
        public ?int $subscriptionsTotalCount = null,
        public ?bool $taxExempt = null,
        public ?bool $hasValidPaymentMethod = null,
        public ?bool $hasPaymentMethodInDunning = null,
        public ?bool $applyCreditToNextRecurringCharge = null,
        public ?array $externalCustomerId = null,
        public ?array $analyticsData = null,
        public ?CarbonImmutable $firstChargeProcessedAt = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Customer data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            email: $data['email'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            phone: $data['phone'] ?? null,
            billingAddress1: $data['billing_address1'] ?? null,
            billingAddress2: $data['billing_address2'] ?? null,
            billingCity: $data['billing_city'] ?? null,
            billingProvince: $data['billing_province'] ?? null,
            billingZip: $data['billing_zip'] ?? null,
            billingCountry: $data['billing_country'] ?? null,
            billingCompany: $data['billing_company'] ?? null,
            billingPhone: $data['billing_phone'] ?? null,
            hash: $data['hash'] ?? null,
            subscriptionsActiveCount: isset($data['subscriptions_active_count'])
                ? (int) $data['subscriptions_active_count']
                : null,
            subscriptionsTotalCount: isset($data['subscriptions_total_count'])
                ? (int) $data['subscriptions_total_count']
                : null,
            taxExempt: isset($data['tax_exempt']) ? (bool) $data['tax_exempt'] : null,
            hasValidPaymentMethod: isset($data['has_valid_payment_method'])
                ? (bool) $data['has_valid_payment_method']
                : null,
            hasPaymentMethodInDunning: isset($data['has_payment_method_in_dunning'])
                ? (bool) $data['has_payment_method_in_dunning']
                : null,
            applyCreditToNextRecurringCharge: isset($data['apply_credit_to_next_recurring_charge'])
                ? (bool) $data['apply_credit_to_next_recurring_charge']
                : null,
            externalCustomerId: $data['external_customer_id'] ?? null,
            analyticsData: $data['analytics_data'] ?? null,
            firstChargeProcessedAt: isset($data['first_charge_processed_at'])
                ? CarbonImmutable::parse($data['first_charge_processed_at'])
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
     * Get full name
     */
    public function getFullName(): string
    {
        return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
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
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'hash' => $this->hash,
            'subscriptions_active_count' => $this->subscriptionsActiveCount,
            'subscriptions_total_count' => $this->subscriptionsTotalCount,
            'tax_exempt' => $this->taxExempt,
            'has_valid_payment_method' => $this->hasValidPaymentMethod,
            'has_payment_method_in_dunning' => $this->hasPaymentMethodInDunning,
            'apply_credit_to_next_recurring_charge' => $this->applyCreditToNextRecurringCharge,
            'external_customer_id' => $this->externalCustomerId,
            'analytics_data' => $this->analyticsData,
            'first_charge_processed_at' => $this->firstChargeProcessedAt?->toIso8601String(),
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
