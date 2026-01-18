<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Webhook Topic enumeration
 *
 * Represents all available webhook topics/events that can be subscribed to in Recharge.
 * These are the event types that trigger webhook notifications.
 *
 * Topics are organized by resource:
 * - Charge events: charge/created, charge/processed, etc.
 * - Subscription events: subscription/created, subscription/updated, etc.
 * - Customer events: customer/created, customer/updated, etc.
 * - Order events: order/created, order/updated, etc.
 * - Address events: address/created, address/updated, etc.
 * - And more...
 *
 * @see https://developer.rechargepayments.com/2021-11/webhooks#available-webhooks
 * @see https://developer.rechargepayments.com/2021-01/webhooks#available-webhooks
 */
enum WebhookTopic: string
{
    // Charge events
    case CHARGE_CREATED = 'charge/created';
    case CHARGE_FAILED = 'charge/failed';
    case CHARGE_PAID = 'charge/paid';
    case CHARGE_MAX_RETRIES_REACHED = 'charge/max_retries_reached';
    case CHARGE_REFUNDED = 'charge/refunded';
    case CHARGE_UPDATED = 'charge/updated';
    case CHARGE_UPCOMING = 'charge/upcoming';
    case CHARGE_DELETED = 'charge/deleted';
    case CHARGE_PROCESSED = 'charge/processed';

    // Subscription events
    case SUBSCRIPTION_CREATED = 'subscription/created';
    case SUBSCRIPTION_ACTIVATED = 'subscription/activated';
    case SUBSCRIPTION_CANCELLED = 'subscription/cancelled';
    case SUBSCRIPTION_UPDATED = 'subscription/updated';
    case SUBSCRIPTION_SKIPPED = 'subscription/skipped';
    case SUBSCRIPTION_UNSKIPPED = 'subscription/unskipped';
    case SUBSCRIPTION_SWAPPED = 'subscription/swapped';
    case SUBSCRIPTION_PAUSED = 'subscription/paused';
    case SUBSCRIPTION_DELETED = 'subscription/deleted';

    // Customer events
    case CUSTOMER_CREATED = 'customer/created';
    case CUSTOMER_ACTIVATED = 'customer/activated';
    case CUSTOMER_DEACTIVATED = 'customer/deactivated';
    case CUSTOMER_UPDATED = 'customer/updated';
    case CUSTOMER_PAYMENT_METHOD_UPDATED = 'customer/payment_method_updated';

    // Order events
    case ORDER_CREATED = 'order/created';
    case ORDER_UPDATED = 'order/updated';
    case ORDER_CANCELLED = 'order/cancelled';
    case ORDER_PAID = 'order/paid';
    case ORDER_FULFILLED = 'order/fulfilled';

    // Address events
    case ADDRESS_CREATED = 'address/created';
    case ADDRESS_UPDATED = 'address/updated';
    case ADDRESS_DELETED = 'address/deleted';

    // Bundle events
    case BUNDLE_SELECTION_CREATED = 'bundle_selection/created';
    case BUNDLE_SELECTION_UPDATED = 'bundle_selection/updated';
    case BUNDLE_SELECTION_DELETED = 'bundle_selection/deleted';

    // Gift purchase events
    case GIFT_PURCHASE_REDEEMED = 'gift_purchase/redeemed';

    // Discount events
    case DISCOUNT_CREATED = 'discount/created';
    case DISCOUNT_UPDATED = 'discount/updated';
    case DISCOUNT_DELETED = 'discount/deleted';

    /**
     * Try to create from string value
     *
     * @param string $value Topic value (e.g., 'charge/created')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Get all topic values as array
     *
     * @return array<int, string> Array of all topic string values
     */
    public static function allValues(): array
    {
        return array_map(
            fn (self $topic): string => $topic->value,
            self::cases()
        );
    }

    /**
     * Get topics grouped by resource type
     *
     * @return array<string, array<string>> Topics grouped by resource (e.g., 'charge' => ['charge/created', ...])
     */
    public static function groupedByResource(): array
    {
        $grouped = [];

        foreach (self::cases() as $topic) {
            $parts = explode('/', $topic->value);
            $resource = $parts[0] ?? 'other';

            if (!isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }

            $grouped[$resource][] = $topic->value;
        }

        return $grouped;
    }
}
