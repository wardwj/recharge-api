<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Interval Unit enumeration
 *
 * Represents the time unit for subscription intervals.
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#the-subscription-object
 */
enum IntervalUnit: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}
