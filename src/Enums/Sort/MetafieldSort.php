<?php

declare(strict_types=1);

namespace Recharge\Enums\Sort;

/**
 * Metafield Sort enumeration
 *
 * Represents valid sort_by values for listing metafields.
 *
 * Note: Sorting for metafields is only available in API version 2021-01.
 * Supported sort options: id and updated_at (ascending and descending).
 *
 * @see https://developer.rechargepayments.com/2021-01/metafields#list-metafields
 */
enum MetafieldSort: string
{
    case ID_ASC = 'id-asc';
    case ID_DESC = 'id-desc';
    case UPDATED_AT_ASC = 'updated_at-asc';
    case UPDATED_AT_DESC = 'updated_at-desc';

    /**
     * Get the default sort order
     */
    public static function default(): self
    {
        return self::ID_DESC;
    }

    /**
     * Try to create from string value
     *
     * @param string $value Sort value (e.g., 'id-desc')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
