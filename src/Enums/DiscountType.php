<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Discount Type enumeration
 *
 * Represents the type of discount value in Recharge discounts.
 *
 * API Version Differences:
 * - 2021-01: Uses "discount_type" with percentage/fixed_amount
 * - 2021-11: Uses "value_type" with percentage/fixed_amount/shipping
 *
 * @see https://developer.rechargepayments.com/2021-11/discounts
 * @see https://developer.rechargepayments.com/2021-01/discounts
 */
enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED_AMOUNT = 'fixed_amount';
    case SHIPPING = 'shipping'; // 2021-11 only

    /**
     * Check if this discount type is supported in given API version
     *
     * @param ApiVersion $version API version to check
     */
    public function isSupportedIn(ApiVersion $version): bool
    {
        if ($this === self::SHIPPING) {
            return $version === ApiVersion::V2021_11;
        }

        return true; // percentage and fixed_amount supported in both
    }
}
