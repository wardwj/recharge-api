<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Payment Type enumeration
 *
 * Represents the type of payment method in Recharge.
 *
 * @see https://developer.rechargepayments.com/2021-11/payment_methods#the-payment-method-object
 */
enum PaymentType: string
{
    case CREDIT_CARD = 'CREDIT_CARD';
    case PAYPAL = 'PAYPAL';
    case APPLE_PAY = 'APPLE_PAY';
    case GOOGLE_PAY = 'GOOGLE_PAY';
    case SEPA_DEBIT = 'SEPA_DEBIT';

    /**
     * Check if this is a credit card payment type
     */
    public function isCreditCard(): bool
    {
        return $this === self::CREDIT_CARD;
    }

    /**
     * Check if this is a digital wallet payment type
     */
    public function isDigitalWallet(): bool
    {
        return $this === self::APPLE_PAY || $this === self::GOOGLE_PAY;
    }
}
