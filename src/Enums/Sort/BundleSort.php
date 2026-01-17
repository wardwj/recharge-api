<?php

declare(strict_types=1);

namespace Recharge\Enums\Sort;

/**
 * Bundle Sort enumeration
 *
 * Represents valid sort_by values for listing bundles.
 *
 * @see https://developer.rechargepayments.com/2021-11/bundles#list-bundles
 * @see https://developer.rechargepayments.com/2021-01/bundles#list-bundles
 */
enum BundleSort: string
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
