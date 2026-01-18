<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Enums\AppliesToProductType;
use Recharge\Enums\DiscountDuration;
use Recharge\Enums\DiscountStatus;
use Recharge\Enums\DiscountType;

/**
 * Discount Data Transfer Object
 *
 * Immutable DTO representing a Recharge discount.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-01: Uses "discount_type" field
 * - 2021-11: Uses "value_type" field (same values as discount_type)
 * - 2021-11: Additional channel_settings structure
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts#the-discount-object
 * @see https://developer.rechargepayments.com/2021-01/discounts#the-discount-object
 */
final readonly class Discount
{
    /**
     * @param int $id Unique numeric identifier for the Discount
     * @param string|null $code Discount code that customers can use
     * @param DiscountType|null $discountType Type of discount (percentage, fixed_amount, shipping)
     * @param int|float|null $value Discount value (percentage or fixed amount)
     * @param DiscountStatus|null $status Status of the discount
     * @param DiscountDuration|null $duration How long the discount applies
     * @param int|null $usageLimit Maximum number of times the discount can be used
     * @param int|null $durationUsageLimit Usage limit for duration period
     * @param int|null $timesUsed Number of times the discount has been used
     * @param AppliesToProductType|null $appliesToProductType Which product types the discount applies to
     * @param int|null $appliesToId ID of specific resource the discount applies to
     * @param string|null $appliesTo Resource type the discount applies to
     * @param string|null $appliesToResource Resource identifier
     * @param int|float|null $prerequisiteSubtotalMin Minimum subtotal required to use discount
     * @param bool|null $oncePerCustomer Whether discount can only be used once per customer
     * @param bool|null $firstTimeCustomerRestriction Whether discount is restricted to first-time customers
     * @param array<string, mixed>|null $channelSettings Channel settings for where discount can be used
     * @param CarbonImmutable|null $startsAt When the discount becomes active
     * @param CarbonImmutable|null $endsAt When the discount expires
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $code = null,
        public ?DiscountType $discountType = null,
        public int|float|null $value = null,
        public ?DiscountStatus $status = null,
        public ?DiscountDuration $duration = null,
        public ?int $usageLimit = null,
        public ?int $durationUsageLimit = null,
        public ?int $timesUsed = null,
        public ?AppliesToProductType $appliesToProductType = null,
        public ?int $appliesToId = null,
        public ?string $appliesTo = null,
        public ?string $appliesToResource = null,
        public int|float|null $prerequisiteSubtotalMin = null,
        public ?bool $oncePerCustomer = null,
        public ?bool $firstTimeCustomerRestriction = null,
        public ?array $channelSettings = null,
        public ?CarbonImmutable $startsAt = null,
        public ?CarbonImmutable $endsAt = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create Discount from API response array
     *
     * @param array<string, mixed> $data API response data
     * @return static Discount instance
     */
    public static function fromArray(array $data): static
    {
        // Handle version differences: 2021-01 uses "discount_type", 2021-11 uses "value_type"
        $discountTypeValue = $data['discount_type'] ?? $data['value_type'] ?? null;

        return new self(
            id: (int) ($data['id'] ?? 0),
            code: $data['code'] ?? null,
            discountType: $discountTypeValue ? DiscountType::tryFrom($discountTypeValue) : null,
            value: isset($data['value']) ? (is_numeric($data['value']) ? (float) $data['value'] : null) : null,
            status: isset($data['status']) ? DiscountStatus::tryFrom($data['status']) : null,
            duration: isset($data['duration']) ? DiscountDuration::tryFrom($data['duration']) : null,
            usageLimit: isset($data['usage_limit']) ? (int) $data['usage_limit'] : null,
            durationUsageLimit: isset($data['duration_usage_limit']) ? (int) $data['duration_usage_limit'] : null,
            timesUsed: isset($data['times_used']) ? (int) $data['times_used'] : null,
            appliesToProductType: isset($data['applies_to_product_type'])
                ? AppliesToProductType::tryFrom($data['applies_to_product_type'])
                : null,
            appliesToId: isset($data['applies_to_id']) ? (int) $data['applies_to_id'] : null,
            appliesTo: isset($data['applies_to'])
                ? (is_string($data['applies_to']) ? $data['applies_to'] : (string) json_encode($data['applies_to']))
                : null,
            appliesToResource: $data['applies_to_resource'] ?? null,
            prerequisiteSubtotalMin: isset($data['prerequisite_subtotal_min'])
                ? (is_numeric($data['prerequisite_subtotal_min']) ? (float) $data['prerequisite_subtotal_min'] : null)
                : null,
            oncePerCustomer: isset($data['once_per_customer']) ? (bool) $data['once_per_customer'] : null,
            firstTimeCustomerRestriction: isset($data['first_time_customer_restriction'])
                ? (bool) $data['first_time_customer_restriction']
                : null,
            channelSettings: $data['channel_settings'] ?? null,
            startsAt: isset($data['starts_at'])
                ? CarbonImmutable::parse($data['starts_at'])
                : null,
            endsAt: isset($data['ends_at'])
                ? CarbonImmutable::parse($data['ends_at'])
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
     * Check if discount is currently active
     */
    public function isActive(): bool
    {
        if ($this->status === null || !$this->status->isEnabled()) {
            return false;
        }

        $now = CarbonImmutable::now();

        if ($this->startsAt !== null && $now->lt($this->startsAt)) {
            return false;
        }

        if ($this->endsAt !== null && $now->gt($this->endsAt)) {
            return false;
        }

        return true;
    }

    /**
     * Check if discount has reached its usage limit
     */
    public function hasReachedUsageLimit(): bool
    {
        if ($this->usageLimit === null) {
            return false;
        }

        return $this->timesUsed !== null && $this->timesUsed >= $this->usageLimit;
    }
}
