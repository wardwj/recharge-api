<?php

declare(strict_types=1);

namespace Recharge\Enums\Sort;

/**
 * Charge Sort enumeration
 *
 * Represents valid sort_by values for listing charges.
 *
 * @see https://developer.rechargepayments.com/2021-11/charges#list-charges
 */
enum ChargeSort: string
{
    case ID_ASC = 'id-asc';
    case ID_DESC = 'id-desc';
    case CREATED_AT_ASC = 'created_at-asc';
    case CREATED_AT_DESC = 'created_at-desc';
    case UPDATED_AT_ASC = 'updated_at-asc';
    case UPDATED_AT_DESC = 'updated_at-desc';
    case SCHEDULED_AT_ASC = 'scheduled_at-asc';
    case SCHEDULED_AT_DESC = 'scheduled_at-desc';

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
