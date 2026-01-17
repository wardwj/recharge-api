<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Store Data Transfer Object
 *
 * Immutable DTO representing a Recharge store.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/store#the-store-object
 * @see https://developer.rechargepayments.com/2021-01/store#the-store-object
 */
final readonly class Store
{
    /**
     * @param int $id Unique numeric identifier for the Store
     * @param string|null $name Store name
     * @param string|null $domain Store domain
     * @param string|null $email Store email
     * @param string|null $currency Store currency code
     * @param array<string, mixed>|null $timezone Store timezone data
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $domain = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $currency = null,
        public ?array $timezone = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Get timezone as string (IANA timezone identifier)
     */
    public function getTimezone(): ?string
    {
        return $this->timezone['iana_timezone'] ?? $this->timezone['name'] ?? null;
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Store data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: $data['name'] ?? null,
            domain: $data['domain'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            currency: $data['currency'] ?? null,
            timezone: $data['timezone'] ?? null,
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain,
            'email' => $this->email,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ];
    }
}
