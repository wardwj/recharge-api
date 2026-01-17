<?php

declare(strict_types=1);

namespace Recharge\Contracts;

/**
 * Data Transfer Object Interface
 *
 * Ensures all DTOs implement standard serialization methods.
 * Promotes consistency across response and request objects.
 */
interface DataTransferObjectInterface
{
    /**
     * Create instance from API response array
     *
     * @param array<string, mixed> $data Raw API data
     */
    public static function fromArray(array $data): static;

    /**
     * Convert to array for API requests or serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Get the raw API response data
     *
     * Useful for accessing fields not explicitly mapped to properties,
     * or for debugging version-specific differences.
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array;
}
