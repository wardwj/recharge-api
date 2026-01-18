<?php

declare(strict_types=1);

namespace Recharge\Support;

/**
 * Sort Validator
 *
 * Validates and normalizes sort_by parameters for Recharge API resources.
 * Handles both enum instances and string values, converting them to validated strings.
 */
final class SortValidator
{
    /**
     * Normalize and validate sort_by parameter in query params
     *
     * Accepts a sort enum instance or string, converts enum to string,
     * and validates the string value against the enum class.
     *
     * @template T of \BackedEnum
     * @param array<string, mixed> $queryParams Query parameters (may be modified)
     * @param class-string<T> $enumClass The sort enum class name (e.g., SubscriptionSort::class)
     * @return array<string, mixed> Modified query parameters with validated sort_by
     * @throws \InvalidArgumentException If sort_by value is invalid
     */
    public static function normalizeAndValidate(array $queryParams, string $enumClass): array
    {
        if (!isset($queryParams['sort_by'])) {
            return $queryParams;
        }

        $sortBy = $queryParams['sort_by'];

        // Handle enum instance - convert to string value
        if ($sortBy instanceof \BackedEnum) {
            $queryParams['sort_by'] = $sortBy->value;

            return $queryParams;
        }

        // Handle string - validate against enum
        if (is_string($sortBy)) {
            self::validateString($sortBy, $enumClass);
        }

        return $queryParams;
    }

    /**
     * Validate a sort_by string value against an enum class
     *
     * @param string $sortBy The sort_by string value to validate
     * @param string $enumClass The enum class name
     * @throws \InvalidArgumentException If the value is not a valid enum case
     */
    private static function validateString(string $sortBy, string $enumClass): void
    {
        $enum = self::tryEnumFromString($sortBy, $enumClass);

        if ($enum !== null) {
            return;
        }

        $allowedValues = self::getAllowedValues($enumClass);

        throw new \InvalidArgumentException(
            sprintf(
                'Invalid sort_by value "%s". Allowed values: %s',
                $sortBy,
                implode(', ', $allowedValues)
            )
        );
    }

    /**
     * Try to create an enum instance from a string value
     *
     * @param string $value The string value
     * @param string $enumClass The enum class name
     * @return \BackedEnum|null The enum instance or null if invalid
     */
    private static function tryEnumFromString(string $value, string $enumClass): ?\BackedEnum
    {
        // Use tryFromString if available (for custom enum methods), otherwise use tryFrom
        if (method_exists($enumClass, 'tryFromString')) {
            return $enumClass::tryFromString($value);
        }

        return $enumClass::tryFrom($value);
    }

    /**
     * Get all allowed values from an enum class
     *
     * @param string $enumClass The enum class name
     * @return array<int, string> Array of allowed string values
     */
    private static function getAllowedValues(string $enumClass): array
    {
        return array_map(
            fn (\BackedEnum $case): string => (string) $case->value,
            $enumClass::cases()
        );
    }
}
