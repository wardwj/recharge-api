<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Order Status enumeration
 *
 * Represents the possible statuses of a Recharge order across both API versions.
 *
 * @see https://developer.rechargepayments.com/2021-11/orders
 * @see https://developer.rechargepayments.com/2021-01/orders
 */
enum OrderStatus: string
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
     * Check if the order was successful
     */
    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if the order failed or has an error
     */
    public function hasError(): bool
    {
        return $this === self::ERROR;
    }

    /**
     * Check if the order was refunded (fully or partially)
     */
    public function isRefunded(): bool
    {
        return $this === self::REFUNDED || $this === self::PARTIALLY_REFUNDED;
    }
}
