<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Payment Method Status enumeration
 *
 * Represents the status of a payment method in Recharge.
 *
 * @see https://developer.rechargepayments.com/2021-11/payment_methods#the-payment-method-object
 */
enum PaymentMethodStatus: string
{
    case VALID = 'valid';
    case INVALID = 'invalid';
    case UNVALIDATED = 'unvalidated';
    case EMPTY = 'empty';

    /**
     * Check if the payment method is valid
     */
    public function isValid(): bool
    {
        return $this === self::VALID;
    }

    /**
     * Check if the payment method is invalid
     */
    public function isInvalid(): bool
    {
        return $this === self::INVALID;
    }
}
