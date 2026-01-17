<?php

declare(strict_types=1);

namespace Recharge\Requests;

use Carbon\CarbonImmutable;
use Recharge\Contracts\ValidatableInterface;
use Recharge\Enums\IntervalUnit;
use Recharge\Exceptions\ValidationException;

/**
 * Create Subscription Request Data
 *
 * Simplified API - pass array or use named arguments
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#create-a-subscription
 */
final readonly class CreateSubscriptionData implements ValidatableInterface
{
    /**
     * @param int $customerId Customer ID
     * @param int|null $addressId Address ID for shipping
     * @param string|null $productTitle Product title
     * @param string|null $variantTitle Variant title
     * @param int $quantity Quantity of items (default: 1)
     * @param string|float|null $price Price
     * @param string $interval Interval shorthand: '1 month', '2 weeks', etc
     * @param CarbonImmutable|null $nextChargeScheduledAt Next charge scheduled date
     * @param array<string, mixed> $additionalData Additional subscription data
     */
    public function __construct(
        public int $customerId,
        public ?int $addressId = null,
        public ?string $productTitle = null,
        public ?string $variantTitle = null,
        public int $quantity = 1,
        public string|float|null $price = null,
        public string $interval = '1 month',
        public ?CarbonImmutable $nextChargeScheduledAt = null,
        public array $additionalData = []
    ) {
    }

    /**
     * Create from array
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'] ?? $data['customerId'],
            addressId: $data['address_id'] ?? $data['addressId'] ?? null,
            productTitle: $data['product_title'] ?? $data['productTitle'] ?? null,
            variantTitle: $data['variant_title'] ?? $data['variantTitle'] ?? null,
            quantity: $data['quantity'] ?? 1,
            price: $data['price'] ?? null,
            interval: $data['interval'] ?? '1 month',
            nextChargeScheduledAt: isset($data['next_charge_scheduled_at'])
                ? CarbonImmutable::parse($data['next_charge_scheduled_at'])
                : null,
            additionalData: $data['additional'] ?? []
        );
    }

    /**
     * Convert to array for API request
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $this->validate();

        [$frequency, $unit] = $this->parseInterval($this->interval);

        $data = array_merge([
            'customer_id' => $this->customerId,
            'address_id' => $this->addressId,
            'product_title' => $this->productTitle,
            'variant_title' => $this->variantTitle,
            'quantity' => $this->quantity,
            'price' => is_float($this->price) ? number_format($this->price, 2, '.', '') : $this->price,
            'order_interval_unit' => $unit->value,
            'order_interval_frequency' => $frequency,
            'charge_interval_frequency' => $frequency,
            'next_charge_scheduled_at' => $this->nextChargeScheduledAt?->toIso8601String(),
        ], $this->additionalData);

        return array_filter($data, fn ($value): bool => $value !== null);
    }

    /**
     * Parse interval string like "1 month", "2 weeks"
     *
     * @param string $interval
     * @return array{int, IntervalUnit}
     */
    /**
     * Parse interval string to frequency and unit
     *
     * @param string $interval Format: "1 month", "2 weeks", etc.
     * @return array{int, IntervalUnit}
     * @throws ValidationException If format is invalid
     */
    private function parseInterval(string $interval): array
    {
        $parts = explode(' ', trim($interval));
        if (count($parts) !== 2) {
            throw new ValidationException(
                'Invalid interval format',
                ['interval' => 'Interval must be in format "1 month", "2 weeks", etc. Got: ' . $interval]
            );
        }

        $frequency = (int) $parts[0];
        $unitStr = rtrim(strtolower($parts[1]), 's'); // Remove plural 's'

        if ($frequency < 1) {
            throw new ValidationException(
                'Invalid interval frequency',
                ['interval' => 'Interval frequency must be at least 1. Got: ' . $frequency]
            );
        }

        $unit = match ($unitStr) {
            'day' => IntervalUnit::DAY,
            'week' => IntervalUnit::WEEK,
            'month' => IntervalUnit::MONTH,
            'year' => IntervalUnit::YEAR,
            default => throw new ValidationException(
                'Invalid interval unit',
                ['interval' => "Interval unit must be one of: day, week, month, year. Got: {$parts[1]}"]
            )
        };

        return [$frequency, $unit];
    }

    /**
     * Validate the request data
     *
     * @throws ValidationException If validation fails
     */
    public function validate(): bool
    {
        $errors = [];

        if ($this->customerId <= 0) {
            $errors['customer_id'] = 'Customer ID must be a positive integer';
        }

        if ($this->quantity < 1) {
            $errors['quantity'] = 'Quantity must be at least 1';
        }

        if ($errors !== []) {
            throw new ValidationException('Subscription validation failed', $errors);
        }

        return true;
    }

    /**
     * Get validation rules
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'min:1'],
            'address_id' => ['nullable', 'integer', 'min:1'],
            'quantity' => ['integer', 'min:1'],
            'interval' => ['required', 'format: "1 month"'],
        ];
    }
}
