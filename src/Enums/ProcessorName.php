<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Processor Name enumeration
 *
 * Represents the payment processor name in Recharge.
 *
 * @see https://developer.rechargepayments.com/2021-11/payment_methods#the-payment-method-object
 */
enum ProcessorName: string
{
    case STRIPE = 'stripe';
    case BRAINTREE = 'braintree';
    case AUTHORIZE = 'authorize';
    case SHOPIFY_PAYMENTS = 'shopify_payments';
    case MOLLIE = 'mollie';

    /**
     * Check if this processor is read-only (managed by external system)
     */
    public function isReadOnly(): bool
    {
        return $this === self::SHOPIFY_PAYMENTS;
    }
}
