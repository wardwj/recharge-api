<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Async Batch Type enumeration
 *
 * Represents all available batch types for async batch operations.
 * Different batch types are available in different API versions.
 *
 * Version-Specific Batch Types:
 * - 2021-01: Includes subscription bulk operations, address discount operations, product operations
 * - 2021-11: Includes plans bulk operations, simplified batch types
 *
 * @see https://developer.rechargepayments.com/2021-11/async_batch_endpoints
 * @see https://developer.rechargepayments.com/2021-01/async_batch_endpoints
 */
enum AsyncBatchType: string
{
    // Discount operations (both versions)
    case DISCOUNT_CREATE = 'discount_create';
    case DISCOUNT_UPDATE = 'discount_update';
    case DISCOUNT_DELETE = 'discount_delete';

    // Plans operations (2021-11)
    case BULK_PLANS_CREATE = 'bulk_plans_create';
    case BULK_PLANS_UPDATE = 'bulk_plans_update';
    case BULK_PLANS_DELETE = 'bulk_plans_delete';

    // One-time operations (both versions)
    case ONETIME_CREATE = 'onetime_create';
    case ONETIME_DELETE = 'onetime_delete';

    // Subscription operations (2021-01)
    case BULK_SUBSCRIPTIONS_CREATE = 'bulk_subscriptions_create';
    case BULK_SUBSCRIPTIONS_UPDATE = 'bulk_subscriptions_update';
    case BULK_SUBSCRIPTIONS_DELETE = 'bulk_subscriptions_delete';
    case SUBSCRIPTION_CANCEL = 'subscription_cancel';

    // Address discount operations (2021-01)
    case ADDRESS_DISCOUNT_APPLY = 'address_discount_apply';
    case ADDRESS_DISCOUNT_REMOVE = 'address_discount_remove';

    // Product operations (2021-01)
    case PRODUCT_CREATE = 'product_create';
    case PRODUCT_UPDATE = 'product_update';
    case PRODUCT_DELETE = 'product_delete';

    /**
     * Get batch types available in a specific API version
     *
     * @param ApiVersion $version API version
     * @return array<self> Array of batch types available in the version
     */
    public static function forVersion(ApiVersion $version): array
    {
        return match ($version) {
            ApiVersion::V2021_01 => [
                self::DISCOUNT_CREATE,
                self::DISCOUNT_UPDATE,
                self::DISCOUNT_DELETE,
                self::ONETIME_CREATE,
                self::ONETIME_DELETE,
                self::BULK_SUBSCRIPTIONS_CREATE,
                self::BULK_SUBSCRIPTIONS_UPDATE,
                self::BULK_SUBSCRIPTIONS_DELETE,
                self::SUBSCRIPTION_CANCEL,
                self::ADDRESS_DISCOUNT_APPLY,
                self::ADDRESS_DISCOUNT_REMOVE,
                self::PRODUCT_CREATE,
                self::PRODUCT_UPDATE,
                self::PRODUCT_DELETE,
            ],
            ApiVersion::V2021_11 => [
                self::DISCOUNT_CREATE,
                self::DISCOUNT_UPDATE,
                self::DISCOUNT_DELETE,
                self::BULK_PLANS_CREATE,
                self::BULK_PLANS_UPDATE,
                self::BULK_PLANS_DELETE,
                self::ONETIME_CREATE,
                self::ONETIME_DELETE,
            ],
        };
    }

    /**
     * Check if this batch type is available in a specific API version
     *
     * @param ApiVersion $version API version
     * @return bool True if available in the version
     */
    public function isAvailableIn(ApiVersion $version): bool
    {
        return in_array($this, self::forVersion($version), true);
    }

    /**
     * Try to create from string value
     *
     * @param string $value Batch type value (e.g., 'discount_create')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
