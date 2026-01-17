<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Enums\ApiVersion;
use Recharge\Enums\AppliesToProductType;
use Recharge\Enums\ChargeStatus;
use Recharge\Enums\DayOfWeek;
use Recharge\Enums\DiscountDuration;
use Recharge\Enums\DiscountStatus;
use Recharge\Enums\DiscountType;
use Recharge\Enums\IntervalUnit;
use Recharge\Enums\OrderStatus;
use Recharge\Enums\SubscriptionStatus;

class EnumTest extends TestCase
{
    // ApiVersion Tests
    public function testApiVersionValues(): void
    {
        $this->assertEquals('2021-01', ApiVersion::V2021_01->value);
        $this->assertEquals('2021-11', ApiVersion::V2021_11->value);
    }

    public function testApiVersionDefault(): void
    {
        $default = ApiVersion::default();
        $this->assertEquals(ApiVersion::V2021_11, $default);
    }

    // IntervalUnit Tests
    public function testIntervalUnitValues(): void
    {
        $this->assertEquals('day', IntervalUnit::DAY->value);
        $this->assertEquals('week', IntervalUnit::WEEK->value);
        $this->assertEquals('month', IntervalUnit::MONTH->value);
        $this->assertEquals('year', IntervalUnit::YEAR->value);
    }

    public function testIntervalUnitFromString(): void
    {
        $this->assertEquals(IntervalUnit::DAY, IntervalUnit::from('day'));
        $this->assertEquals(IntervalUnit::WEEK, IntervalUnit::from('week'));
        $this->assertEquals(IntervalUnit::MONTH, IntervalUnit::from('month'));
        $this->assertEquals(IntervalUnit::YEAR, IntervalUnit::from('year'));
    }

    // SubscriptionStatus Tests
    public function testSubscriptionStatusValues(): void
    {
        $this->assertEquals('ACTIVE', SubscriptionStatus::ACTIVE->value);
        $this->assertEquals('CANCELLED', SubscriptionStatus::CANCELLED->value);
        $this->assertEquals('EXPIRED', SubscriptionStatus::EXPIRED->value);
    }

    public function testSubscriptionStatusHelperMethods(): void
    {
        $this->assertTrue(SubscriptionStatus::ACTIVE->isActive());
        $this->assertFalse(SubscriptionStatus::CANCELLED->isActive());

        $this->assertTrue(SubscriptionStatus::CANCELLED->isCancelled());
        $this->assertFalse(SubscriptionStatus::ACTIVE->isCancelled());
    }

    public function testSubscriptionStatusTryFromString(): void
    {
        $this->assertEquals(SubscriptionStatus::ACTIVE, SubscriptionStatus::tryFromString('active'));
        $this->assertEquals(SubscriptionStatus::ACTIVE, SubscriptionStatus::tryFromString('ACTIVE'));
        $this->assertEquals(SubscriptionStatus::CANCELLED, SubscriptionStatus::tryFromString('cancelled'));
        $this->assertNull(SubscriptionStatus::tryFromString('invalid'));
    }

    // OrderStatus Tests
    public function testOrderStatusValues(): void
    {
        $this->assertEquals('QUEUED', OrderStatus::QUEUED->value);
        $this->assertEquals('SUCCESS', OrderStatus::SUCCESS->value);
        $this->assertEquals('ERROR', OrderStatus::ERROR->value);
        $this->assertEquals('REFUNDED', OrderStatus::REFUNDED->value);
        $this->assertEquals('PARTIALLY_REFUNDED', OrderStatus::PARTIALLY_REFUNDED->value);
        $this->assertEquals('SKIPPED', OrderStatus::SKIPPED->value);
    }

    public function testOrderStatusHelperMethods(): void
    {
        $this->assertTrue(OrderStatus::SUCCESS->isSuccess());
        $this->assertFalse(OrderStatus::ERROR->isSuccess());

        $this->assertTrue(OrderStatus::ERROR->hasError());
        $this->assertFalse(OrderStatus::SUCCESS->hasError());

        $this->assertTrue(OrderStatus::REFUNDED->isRefunded());
        $this->assertTrue(OrderStatus::PARTIALLY_REFUNDED->isRefunded());
        $this->assertFalse(OrderStatus::SUCCESS->isRefunded());
    }

    public function testOrderStatusTryFromString(): void
    {
        $this->assertEquals(OrderStatus::SUCCESS, OrderStatus::tryFromString('success'));
        $this->assertEquals(OrderStatus::SUCCESS, OrderStatus::tryFromString('SUCCESS'));
        $this->assertEquals(OrderStatus::QUEUED, OrderStatus::tryFromString('queued'));
        $this->assertNull(OrderStatus::tryFromString('invalid'));
    }

    // ChargeStatus Tests
    public function testChargeStatusValues(): void
    {
        $this->assertEquals('QUEUED', ChargeStatus::QUEUED->value);
        $this->assertEquals('SUCCESS', ChargeStatus::SUCCESS->value);
        $this->assertEquals('ERROR', ChargeStatus::ERROR->value);
    }

    public function testChargeStatusHelperMethods(): void
    {
        $this->assertTrue(ChargeStatus::SUCCESS->isSuccess());
        $this->assertTrue(ChargeStatus::ERROR->hasError());
        $this->assertTrue(ChargeStatus::REFUNDED->isRefunded());
        $this->assertTrue(ChargeStatus::QUEUED->isPending());
    }

    public function testChargeStatusTryFromString(): void
    {
        $this->assertEquals(ChargeStatus::SUCCESS, ChargeStatus::tryFromString('success'));
        $this->assertEquals(ChargeStatus::ERROR, ChargeStatus::tryFromString('ERROR'));
        $this->assertNull(ChargeStatus::tryFromString('invalid'));
    }

    // DiscountType Tests
    public function testDiscountTypeValues(): void
    {
        $this->assertEquals('percentage', DiscountType::PERCENTAGE->value);
        $this->assertEquals('fixed_amount', DiscountType::FIXED_AMOUNT->value);
        $this->assertEquals('shipping', DiscountType::SHIPPING->value);
    }

    public function testDiscountTypeVersionSupport(): void
    {
        // percentage and fixed_amount supported in both versions
        $this->assertTrue(DiscountType::PERCENTAGE->isSupportedIn(ApiVersion::V2021_01));
        $this->assertTrue(DiscountType::PERCENTAGE->isSupportedIn(ApiVersion::V2021_11));
        $this->assertTrue(DiscountType::FIXED_AMOUNT->isSupportedIn(ApiVersion::V2021_01));
        $this->assertTrue(DiscountType::FIXED_AMOUNT->isSupportedIn(ApiVersion::V2021_11));

        // shipping only in 2021-11
        $this->assertFalse(DiscountType::SHIPPING->isSupportedIn(ApiVersion::V2021_01));
        $this->assertTrue(DiscountType::SHIPPING->isSupportedIn(ApiVersion::V2021_11));
    }

    // DiscountStatus Tests
    public function testDiscountStatusValues(): void
    {
        $this->assertEquals('enabled', DiscountStatus::ENABLED->value);
        $this->assertEquals('disabled', DiscountStatus::DISABLED->value);
        $this->assertEquals('fully_disabled', DiscountStatus::FULLY_DISABLED->value);
    }

    public function testDiscountStatusHelperMethods(): void
    {
        $this->assertTrue(DiscountStatus::ENABLED->isEnabled());
        $this->assertFalse(DiscountStatus::DISABLED->isEnabled());

        $this->assertTrue(DiscountStatus::DISABLED->isDisabled());
        $this->assertTrue(DiscountStatus::FULLY_DISABLED->isDisabled());
        $this->assertFalse(DiscountStatus::ENABLED->isDisabled());
    }

    // DiscountDuration Tests
    public function testDiscountDurationValues(): void
    {
        $this->assertEquals('forever', DiscountDuration::FOREVER->value);
        $this->assertEquals('usage_limit', DiscountDuration::USAGE_LIMIT->value);
        $this->assertEquals('single_use', DiscountDuration::SINGLE_USE->value);
    }

    public function testDiscountDurationHelperMethods(): void
    {
        $this->assertTrue(DiscountDuration::FOREVER->isForever());
        $this->assertFalse(DiscountDuration::SINGLE_USE->isForever());

        $this->assertTrue(DiscountDuration::USAGE_LIMIT->isLimited());
        $this->assertTrue(DiscountDuration::SINGLE_USE->isLimited());
        $this->assertFalse(DiscountDuration::FOREVER->isLimited());
    }

    // AppliesToProductType Tests
    public function testAppliesToProductTypeValues(): void
    {
        $this->assertEquals('ALL', AppliesToProductType::ALL->value);
        $this->assertEquals('ONETIME', AppliesToProductType::ONETIME->value);
        $this->assertEquals('SUBSCRIPTION', AppliesToProductType::SUBSCRIPTION->value);
    }

    public function testAppliesToProductTypeHelperMethods(): void
    {
        // ALL applies to everything
        $this->assertTrue(AppliesToProductType::ALL->appliesToAll());
        $this->assertTrue(AppliesToProductType::ALL->appliesToSubscriptions());
        $this->assertTrue(AppliesToProductType::ALL->appliesToOnetime());

        // SUBSCRIPTION
        $this->assertFalse(AppliesToProductType::SUBSCRIPTION->appliesToAll());
        $this->assertTrue(AppliesToProductType::SUBSCRIPTION->appliesToSubscriptions());
        $this->assertFalse(AppliesToProductType::SUBSCRIPTION->appliesToOnetime());

        // ONETIME
        $this->assertFalse(AppliesToProductType::ONETIME->appliesToAll());
        $this->assertFalse(AppliesToProductType::ONETIME->appliesToSubscriptions());
        $this->assertTrue(AppliesToProductType::ONETIME->appliesToOnetime());
    }

    // DayOfWeek Tests
    public function testDayOfWeekValues(): void
    {
        $this->assertEquals(0, DayOfWeek::SUNDAY->value);
        $this->assertEquals(1, DayOfWeek::MONDAY->value);
        $this->assertEquals(2, DayOfWeek::TUESDAY->value);
        $this->assertEquals(3, DayOfWeek::WEDNESDAY->value);
        $this->assertEquals(4, DayOfWeek::THURSDAY->value);
        $this->assertEquals(5, DayOfWeek::FRIDAY->value);
        $this->assertEquals(6, DayOfWeek::SATURDAY->value);
    }

    public function testDayOfWeekNames(): void
    {
        $this->assertEquals('Sunday', DayOfWeek::SUNDAY->name());
        $this->assertEquals('Monday', DayOfWeek::MONDAY->name());
        $this->assertEquals('Saturday', DayOfWeek::SATURDAY->name());
    }

    public function testDayOfWeekHelperMethods(): void
    {
        // Weekend
        $this->assertTrue(DayOfWeek::SATURDAY->isWeekend());
        $this->assertTrue(DayOfWeek::SUNDAY->isWeekend());
        $this->assertFalse(DayOfWeek::MONDAY->isWeekend());

        // Weekday
        $this->assertTrue(DayOfWeek::MONDAY->isWeekday());
        $this->assertTrue(DayOfWeek::FRIDAY->isWeekday());
        $this->assertFalse(DayOfWeek::SATURDAY->isWeekday());
        $this->assertFalse(DayOfWeek::SUNDAY->isWeekday());
    }
}
