<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * API Version enumeration
 *
 * Represents supported Recharge Payments API versions.
 */
enum ApiVersion: string
{
    case V2021_01 = '2021-01';
    case V2021_11 = '2021-11';

    /**
     * Get the default API version
     */
    public static function default(): self
    {
        return self::V2021_11;
    }

    /**
     * Get all supported API versions as strings
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
