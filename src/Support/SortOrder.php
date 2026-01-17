<?php

declare(strict_types=1);

namespace Recharge\Support;

/**
 * Sort order helper for validating sort_by query parameters
 *
 * Provides validation for sort_by values across different Recharge API resources.
 * Each resource may support different sort fields and directions.
 */
final class SortOrder
{
    /**
     * Valid sort_by values for Subscriptions resource
     *
     * @var array<string>
     */
    public const SUBSCRIPTIONS = [
        'id-asc',
        'id-desc',
        'created_at-asc',
        'created_at-desc',
        'updated_at-asc',
        'updated_at-desc',
    ];

    /**
     * Valid sort_by values for Charges resource
     *
     * @var array<string>
     */
    public const CHARGES = [
        'id-asc',
        'id-desc',
        'created_at-asc',
        'created_at-desc',
        'updated_at-asc',
        'updated_at-desc',
        'scheduled_at-asc',
        'scheduled_at-desc',
    ];

    /**
     * Valid sort_by values for Orders resource
     *
     * @var array<string>
     */
    public const ORDERS = [
        'id-asc',
        'id-desc',
        'created_at-asc',
        'created_at-desc',
        'updated_at-asc',
        'updated_at-desc',
        'scheduled_at-asc',
        'scheduled_at-desc',
    ];

    /**
     * Valid sort_by values for Customers resource
     *
     * @var array<string>
     */
    public const CUSTOMERS = [
        'id-asc',
        'id-desc',
        'created_at-asc',
        'created_at-desc',
        'updated_at-asc',
        'updated_at-desc',
    ];

    /**
     * Validate a sort_by value for a given resource
     *
     * @param string|null $sortBy The sort_by value to validate
     * @param array<string> $allowedValues Array of allowed sort_by values
     * @return void
     * @throws \InvalidArgumentException If sort_by is invalid
     */
    public static function validate(?string $sortBy, array $allowedValues): void
    {
        if ($sortBy === null) {
            return;
        }

        if (!in_array($sortBy, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid sort_by value "%s". Allowed values: %s',
                    $sortBy,
                    implode(', ', $allowedValues)
                )
            );
        }
    }

    /**
     * Get default sort_by value for a resource
     *
     * @param array<string> $allowedValues Array of allowed sort_by values
     * @return string Default sort_by value (typically id-desc)
     */
    public static function getDefault(array $allowedValues): string
    {
        // Default is typically id-desc
        if (in_array('id-desc', $allowedValues, true)) {
            return 'id-desc';
        }

        // Fallback to first value if id-desc not available
        return $allowedValues[0] ?? 'id-desc';
    }
}
