<?php

declare(strict_types=1);

namespace Recharge\Enums\Sort;

/**
 * Async Batch Sort enumeration
 *
 * Represents valid sort_by values for listing async batches.
 * Based on Recharge API sorting documentation.
 *
 * @see https://developer.rechargepayments.com/2021-11/sorting
 * @see https://developer.rechargepayments.com/2021-01/sorting
 */
enum AsyncBatchSort: string
{
    case ID_ASC = 'id-asc';
    case ID_DESC = 'id-desc';
    case CREATED_AT_ASC = 'created_at-asc';
    case CREATED_AT_DESC = 'created_at-desc';

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
