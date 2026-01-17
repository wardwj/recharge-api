<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Charge Status enumeration
 *
 * Represents the possible statuses of a Recharge charge across both API versions.
 *
 * @see https://developer.rechargepayments.com/2021-11/charges
 * @see https://developer.rechargepayments.com/2021-01/charges
 */
enum ChargeStatus: string
{
    case QUEUED = 'QUEUED';
    case SUCCESS = 'SUCCESS';
    case ERROR = 'ERROR';
    case REFUNDED = 'REFUNDED';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    case SKIPPED = 'SKIPPED';

    /**
     * Try to create from string value (case-insensitive)
     * Handles both uppercase (2021-01) and lowercase (2021-11) values
     *
     * @param string $value Status value
     * @return self|null
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtoupper($value));
    }

    /**
     * Check if the charge was successful
     */
    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if the charge failed or has an error
     */
    public function hasError(): bool
    {
        return $this === self::ERROR;
    }

    /**
     * Check if the charge was refunded (fully or partially)
     */
    public function isRefunded(): bool
    {
        return $this === self::REFUNDED || $this === self::PARTIALLY_REFUNDED;
    }

    /**
     * Check if charge is pending processing
     */
    public function isPending(): bool
    {
        return $this === self::QUEUED;
    }
}
