<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;

/**
 * Metafield Data Transfer Object
 *
 * Immutable DTO representing a Recharge metafield.
 * Metafields allow you to store custom key-value pairs for various resources.
 *
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/metafields#the-metafield-object
 * @see https://developer.rechargepayments.com/2021-01/metafields#the-metafield-object
 */
final readonly class Metafield
{
    /**
     * @param int $id Unique numeric identifier for the Metafield
     * @param string $ownerResource The resource type that owns this metafield (e.g., "customer", "subscription", "charge")
     * @param int $ownerId The ID of the resource that owns this metafield
     * @param string $namespace The namespace for organizing metafields
     * @param string $key The key/name of the metafield
     * @param string|int|float|bool|array<string, mixed>|null $value The value of the metafield (can be string, number, boolean, or JSON)
     * @param string|null $type The type of the metafield value (e.g., "single_line_text_field", "number_integer")
     * @param string|null $description Description of the metafield
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public string $ownerResource,
        public int $ownerId,
        public string $namespace,
        public string $key,
        public string|int|float|bool|array|null $value = null,
        public ?string $type = null,
        public ?string $description = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Metafield data from API
     */
    public static function fromArray(array $data): static
    {
        $value = $data['value'] ?? null;

        // Handle value conversion - API may return JSON string or parsed value
        if (is_string($value) && ($decoded = json_decode($value, true)) !== null && json_last_error() === JSON_ERROR_NONE) {
            $value = $decoded;
        }

        return new self(
            id: (int) ($data['id'] ?? 0),
            ownerResource: $data['owner_resource'] ?? '',
            ownerId: (int) ($data['owner_id'] ?? 0),
            namespace: $data['namespace'] ?? '',
            key: $data['key'] ?? '',
            value: $value,
            type: $data['type'] ?? null,
            description: $data['description'] ?? null,
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
        $value = $this->value;

        // Convert array values to JSON string if needed
        if (is_array($value)) {
            $value = json_encode($value);
        }

        return array_filter([
            'id' => $this->id,
            'owner_resource' => $this->ownerResource,
            'owner_id' => $this->ownerId,
            'namespace' => $this->namespace,
            'key' => $this->key,
            'value' => $value,
            'type' => $this->type,
            'description' => $this->description,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }
}
