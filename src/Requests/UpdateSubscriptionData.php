<?php

declare(strict_types=1);

namespace Recharge\Requests;

use Carbon\CarbonImmutable;
use Recharge\Enums\IntervalUnit;
use Recharge\Enums\SubscriptionStatus;

/**
 * Update Subscription Request Data
 *
 * Data transfer object for updating an existing subscription.
 * All fields are optional - only provided fields will be updated.
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#update-a-subscription
 */
final readonly class UpdateSubscriptionData
{
    /**
     * @param int|null $addressId Address ID for shipping
     * @param string|null $productTitle Product title
     * @param string|null $variantTitle Variant title
     * @param int|null $quantity Quantity of items
     * @param string|null $price Price as string
     * @param SubscriptionStatus|null $status Subscription status
     * @param IntervalUnit|null $orderIntervalUnit Order interval unit
     * @param int|null $orderIntervalFrequency Order interval frequency
     * @param int|null $orderDayOfMonth Order day of month (1-31)
     * @param int|null $orderDayOfWeek Order day of week (0-6, where 0 is Sunday)
     * @param int|null $chargeIntervalFrequency Charge interval frequency
     * @param CarbonImmutable|null $nextChargeScheduledAt Next charge scheduled date
     * @param array<string, mixed> $additionalData Additional subscription data
     */
    public function __construct(
        public ?int $addressId = null,
        public ?string $productTitle = null,
        public ?string $variantTitle = null,
        public ?int $quantity = null,
        public ?string $price = null,
        public ?SubscriptionStatus $status = null,
        public ?IntervalUnit $orderIntervalUnit = null,
        public ?int $orderIntervalFrequency = null,
        public ?int $orderDayOfMonth = null,
        public ?int $orderDayOfWeek = null,
        public ?int $chargeIntervalFrequency = null,
        public ?CarbonImmutable $nextChargeScheduledAt = null,
        public array $additionalData = []
    ) {
    }

    /**
     * Convert to array for API request
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = array_merge([
            'address_id' => $this->addressId,
            'product_title' => $this->productTitle,
            'variant_title' => $this->variantTitle,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'status' => $this->status?->value,
            'order_interval_unit' => $this->orderIntervalUnit?->value,
            'order_interval_frequency' => $this->orderIntervalFrequency,
            'order_day_of_month' => $this->orderDayOfMonth,
            'order_day_of_week' => $this->orderDayOfWeek,
            'charge_interval_frequency' => $this->chargeIntervalFrequency,
            'next_charge_scheduled_at' => $this->nextChargeScheduledAt?->toIso8601String(),
        ], $this->additionalData);

        // Remove null values to keep payload clean
        return array_filter($data, fn ($value): bool => $value !== null);
    }
}
