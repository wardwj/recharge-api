<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Address Data Transfer Object
 *
 * Immutable DTO representing a Recharge address.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * Version-Specific Fields:
 * - 2021-11: payment_method_id, country_code
 *
 * @see https://developer.rechargepayments.com/2021-11/addresses#the-address-object
 * @see https://developer.rechargepayments.com/2021-01/addresses#the-address-object
 */
final readonly class Address
{
    /**
     * @param int $id Unique numeric identifier for the Address
     * @param int $customerId Unique numeric identifier of the customer
     * @param string|null $firstName First name
     * @param string|null $lastName Last name
     * @param string|null $address1 Address line 1
     * @param string|null $address2 Address line 2
     * @param string|null $city City
     * @param string|null $province Province/State code
     * @param string|null $zip Zip/Postal code
     * @param string|null $country Country name
     * @param string|null $countryCode Country code (2021-11+)
     * @param string|null $phone Phone number
     * @param int|null $paymentMethodId Payment method ID (2021-11+)
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $address1 = null,
        public ?string $address2 = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $zip = null,
        public ?string $country = null,
        public ?string $countryCode = null,
        public ?string $phone = null,
        public ?int $paymentMethodId = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Address data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            address1: $data['address1'] ?? null,
            address2: $data['address2'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            zip: $data['zip'] ?? null,
            country: $data['country'] ?? null,
            countryCode: $data['country_code'] ?? null,
            phone: $data['phone'] ?? null,
            paymentMethodId: isset($data['payment_method_id']) ? (int) $data['payment_method_id'] : null,
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
            'customer_id' => $this->customerId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'province' => $this->province,
            'zip' => $this->zip,
            'country' => $this->country,
            'country_code' => $this->countryCode,
            'phone' => $this->phone,
            'payment_method_id' => $this->paymentMethodId,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
