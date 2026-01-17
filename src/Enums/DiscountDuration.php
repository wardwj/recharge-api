<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Discount Duration enumeration
 *
 * Represents how long a discount applies to orders.
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts
 * @see https://developer.rechargepayments.com/2021-01/discounts
 */
enum DiscountDuration: string
{
    case FOREVER = 'forever';
    case USAGE_LIMIT = 'usage_limit';
    case SINGLE_USE = 'single_use';

    /**
     * Check if discount applies indefinitely
     */
    public function isForever(): bool
    {
        return $this === self::FOREVER;
    }

    /**
     * Check if discount has limited uses
     */
    public function isLimited(): bool
    {
        return $this === self::USAGE_LIMIT || $this === self::SINGLE_USE;
    }
}
