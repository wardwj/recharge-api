<?php

declare(strict_types=1);

namespace Recharge\Enums;

/**
 * Day of Week enumeration
 *
 * Used for order_day_of_week in subscriptions (0 = Sunday, 6 = Saturday).
 *
 * @see https://developer.rechargepayments.com/2021-11/subscriptions
 * @see https://developer.rechargepayments.com/2021-01/subscriptions
 */
enum DayOfWeek: int
{
    case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

    /**
     * Get day name
     */
    public function name(): string
    {
        return match ($this) {
            self::SUNDAY => 'Sunday',
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
        };
    }

    /**
     * Check if it's a weekend day
     */
    public function isWeekend(): bool
    {
        return $this === self::SATURDAY || $this === self::SUNDAY;
    }

    /**
     * Check if it's a weekday
     */
    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }
}
