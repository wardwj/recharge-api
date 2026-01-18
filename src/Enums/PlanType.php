<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Plan Type enumeration
 *
 * Represents the type of plan in Recharge.
 *
 * @see https://developer.rechargepayments.com/2021-11/plans#the-plan-object
 */
enum PlanType: string
{
    case SUBSCRIPTION = 'subscription';
    case PREPAID = 'prepaid';
    case ONETIME = 'onetime';

    /**
     * Check if this is a subscription plan
     */
    public function isSubscription(): bool
    {
        return $this === self::SUBSCRIPTION;
    }

    /**
     * Check if this is a prepaid plan
     */
    public function isPrepaid(): bool
    {
        return $this === self::PREPAID;
    }

    /**
     * Check if this is a one-time plan
     */
    public function isOnetime(): bool
    {
        return $this === self::ONETIME;
    }
}
