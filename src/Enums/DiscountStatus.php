<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Discount Status enumeration
 *
 * Represents the status of a Recharge discount.
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts
 * @see https://developer.rechargepayments.com/2021-01/discounts
 */
enum DiscountStatus: string
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case FULLY_DISABLED = 'fully_disabled';

    /**
     * Check if discount is enabled
     */
    public function isEnabled(): bool
    {
        return $this === self::ENABLED;
    }

    /**
     * Check if discount is disabled (any type)
     */
    public function isDisabled(): bool
    {
        return $this === self::DISABLED || $this === self::FULLY_DISABLED;
    }
}
