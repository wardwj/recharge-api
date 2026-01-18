<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Credit Data Transfer Object
 *
 * Immutable DTO representing a Recharge store credit.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Store credits are applied to customer accounts and can be used
 * to offset future charges.
 *
 * @see https://developer.rechargepayments.com/2021-11/credits#the-credit-object
 * @see https://developer.rechargepayments.com/2021-01/credits#the-credit-object
 */
final readonly class Credit
{
    /**
     * @param int $id Unique numeric identifier for the Credit
     * @param int $customerId Unique numeric identifier of the customer
     * @param string|float|null $amount Credit amount
     * @param string|null $currency Currency code (e.g., "USD")
     * @param string|null $note Note or description for the credit
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public string|float|null $amount = null,
        public ?string $currency = null,
        public ?string $note = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Credit data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            note: $data['note'] ?? null,
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
            'amount' => $this->amount,
            'currency' => $this->currency,
            'note' => $this->note,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
