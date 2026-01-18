<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;
use Recharge\Enums\PlanType;

/**
 * Plan Data Transfer Object
 *
 * Immutable DTO representing a Recharge plan.
 * Plans are only available in API version 2021-11.
 * They replace the deprecated products endpoint for plan-related operations in 2021-01.
 *
 * @see https://developer.rechargepayments.com/2021-11/plans#the-plan-object
 */
final readonly class Plan implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Plan
     * @param PlanType|null $type Plan type (subscription, prepaid, onetime)
     * @param string|null $title Plan title
     * @param array<string, mixed>|null $externalProductId External product ID object
     * @param array<string, mixed>|null $externalVariantIds External variant IDs array
     * @param bool|null $hasVariantRestrictions Whether plan has variant restrictions
     * @param array<string, mixed>|null $subscriptionPreferences Subscription preferences (charge_interval_frequency, interval_unit, order_interval_frequency, etc.)
     * @param string|float|null $discountAmount Discount amount
     * @param string|null $discountType Discount type (e.g., 'percentage')
     * @param int|null $sortOrder Sort order for display
     * @param array<string, mixed>|null $channelSettings Channel visibility settings
     * @param string|null $externalPlanId External plan ID
     * @param string|null $externalPlanName External plan name
     * @param string|null $externalPlanGroupId External plan group ID
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param CarbonImmutable|null $deletedAt Deleted timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?PlanType $type = null,
        public ?string $title = null,
        public ?array $externalProductId = null,
        public ?array $externalVariantIds = null,
        public ?bool $hasVariantRestrictions = null,
        public ?array $subscriptionPreferences = null,
        public string|float|null $discountAmount = null,
        public ?string $discountType = null,
        public ?int $sortOrder = null,
        public ?array $channelSettings = null,
        public ?string $externalPlanId = null,
        public ?string $externalPlanName = null,
        public ?string $externalPlanGroupId = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public ?CarbonImmutable $deletedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Plan data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            type: isset($data['type']) ? PlanType::tryFrom($data['type']) : null,
            title: $data['title'] ?? null,
            externalProductId: $data['external_product_id'] ?? null,
            externalVariantIds: $data['external_variant_ids'] ?? null,
            hasVariantRestrictions: isset($data['has_variant_restrictions']) ? (bool) $data['has_variant_restrictions'] : null,
            subscriptionPreferences: $data['subscription_preferences'] ?? null,
            discountAmount: isset($data['discount_amount']) ? (is_numeric($data['discount_amount']) ? (string) $data['discount_amount'] : $data['discount_amount']) : null,
            discountType: $data['discount_type'] ?? null,
            sortOrder: isset($data['sort_order']) ? (int) $data['sort_order'] : null,
            channelSettings: $data['channel_settings'] ?? null,
            externalPlanId: $data['external_plan_id'] ?? null,
            externalPlanName: $data['external_plan_name'] ?? null,
            externalPlanGroupId: $data['external_plan_group_id'] ?? null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            deletedAt: isset($data['deleted_at'])
                ? CarbonImmutable::parse($data['deleted_at'])
                : null,
            rawData: $data
        );
    }

    /**
     * Check if this plan is deleted
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    /**
     * Get charge interval frequency from subscription preferences
     */
    public function getChargeIntervalFrequency(): ?int
    {
        if (is_array($this->subscriptionPreferences)) {
            return isset($this->subscriptionPreferences['charge_interval_frequency'])
                ? (int) $this->subscriptionPreferences['charge_interval_frequency']
                : null;
        }

        return null;
    }

    /**
     * Get order interval frequency from subscription preferences
     */
    public function getOrderIntervalFrequency(): ?int
    {
        if (is_array($this->subscriptionPreferences)) {
            return isset($this->subscriptionPreferences['order_interval_frequency'])
                ? (int) $this->subscriptionPreferences['order_interval_frequency']
                : null;
        }

        return null;
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
            'type' => $this->type?->value,
            'title' => $this->title,
            'external_product_id' => $this->externalProductId,
            'external_variant_ids' => $this->externalVariantIds,
            'has_variant_restrictions' => $this->hasVariantRestrictions,
            'subscription_preferences' => $this->subscriptionPreferences,
            'discount_amount' => $this->discountAmount,
            'discount_type' => $this->discountType,
            'sort_order' => $this->sortOrder,
            'channel_settings' => $this->channelSettings,
            'external_plan_id' => $this->externalPlanId,
            'external_plan_name' => $this->externalPlanName,
            'external_plan_group_id' => $this->externalPlanGroupId,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'deleted_at' => $this->deletedAt?->toIso8601String(),
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
