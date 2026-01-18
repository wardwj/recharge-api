<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Collection Sort Order enumeration
 *
 * Represents valid sort_order values for collections.
 *
 * @see https://developer.rechargepayments.com/2021-11/collections#the-collection-object
 */
enum CollectionSortOrder: string
{
    case ID_ASC = 'id-asc';
    case ID_DESC = 'id-desc';
    case TITLE_ASC = 'title-asc';
    case TITLE_DESC = 'title-desc';
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
     * @param string $value Sort order value (e.g., 'id-desc')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
