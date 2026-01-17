<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Subscription Status enumeration
 *
 * Represents the possible statuses of a Recharge subscription.
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#the-subscription-object
 */
enum SubscriptionStatus: string
{
    case ACTIVE = 'ACTIVE';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
    case PAUSED = 'PAUSED';
    case QUEUED = 'QUEUED';
    case SKIPPED = 'SKIPPED';
    case UNPAID = 'UNPAID';

    /**
     * Check if the status represents an active subscription
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if the status represents a cancelled subscription
     */
    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    /**
     * Try to create from string value (case-insensitive)
     *
     * @param string $value Status value
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        $upperValue = strtoupper($value);

        return self::tryFrom($upperValue);
    }
}
