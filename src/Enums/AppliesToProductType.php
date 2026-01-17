<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Applies To Product Type enumeration
 *
 * Defines which type of products a discount applies to.
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts
 * @see https://developer.rechargepayments.com/2021-01/discounts
 */
enum AppliesToProductType: string
{
    case ALL = 'ALL';
    case ONETIME = 'ONETIME';
    case SUBSCRIPTION = 'SUBSCRIPTION';

    /**
     * Check if discount applies to all product types
     */
    public function appliesToAll(): bool
    {
        return $this === self::ALL;
    }

    /**
     * Check if discount applies to subscriptions
     */
    public function appliesToSubscriptions(): bool
    {
        return $this === self::ALL || $this === self::SUBSCRIPTION;
    }

    /**
     * Check if discount applies to one-time purchases
     */
    public function appliesToOnetime(): bool
    {
        return $this === self::ALL || $this === self::ONETIME;
    }
}
