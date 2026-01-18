<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Notification Template enumeration
 *
 * Represents valid notification template types for sending customer notifications.
 * These templates are supported via the POST /customers/{id}/notifications endpoint.
 *
 * Supported templates (both API versions 2021-01 and 2021-11):
 * - get_account_access - Send account access link/code to customer
 * - upcoming_charge - Send notification about upcoming recurring charge
 *
 * @see https://developer.rechargepayments.com/2021-11/customers#send-a-notification
 * @see https://developer.rechargepayments.com/2021-01/customers#send-a-notification
 */
enum NotificationTemplate: string
{
    case GET_ACCOUNT_ACCESS = 'get_account_access';
    case UPCOMING_CHARGE = 'upcoming_charge';

    /**
     * Try to create from string value
     *
     * @param string $value Template value (e.g., 'get_account_access')
     * @return self|null Returns null if value doesn't match
     */
    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
