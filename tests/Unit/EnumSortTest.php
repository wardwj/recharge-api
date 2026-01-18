<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Enums\Sort\BundleSort;
use Recharge\Enums\Sort\ChargeSort;
use Recharge\Enums\Sort\CustomerSort;
use Recharge\Enums\Sort\MetafieldSort;
use Recharge\Enums\Sort\OneTimeSort;
use Recharge\Enums\Sort\OrderSort;
use Recharge\Enums\Sort\SubscriptionSort;

class EnumSortTest extends TestCase
{
    public function testSubscriptionSortEnum(): void
    {
        $this->assertEquals('id-desc', SubscriptionSort::ID_DESC->value);
        $this->assertEquals('created_at-asc', SubscriptionSort::CREATED_AT_ASC->value);
        $this->assertEquals('updated_at-desc', SubscriptionSort::UPDATED_AT_DESC->value);
    }

    public function testSubscriptionSortDefault(): void
    {
        $default = SubscriptionSort::default();
        $this->assertEquals(SubscriptionSort::ID_DESC, $default);
    }

    public function testSubscriptionSortTryFromString(): void
    {
        $this->assertEquals(SubscriptionSort::ID_DESC, SubscriptionSort::tryFromString('id-desc'));
        $this->assertEquals(SubscriptionSort::CREATED_AT_ASC, SubscriptionSort::tryFromString('created_at-asc'));
        $this->assertNull(SubscriptionSort::tryFromString('invalid-sort'));
    }

    public function testChargeSortEnum(): void
    {
        $this->assertEquals('id-desc', ChargeSort::ID_DESC->value);
        $this->assertEquals('scheduled_at-asc', ChargeSort::SCHEDULED_AT_ASC->value);
        $this->assertEquals('scheduled_at-desc', ChargeSort::SCHEDULED_AT_DESC->value);
    }

    public function testChargeSortDefault(): void
    {
        $default = ChargeSort::default();
        $this->assertEquals(ChargeSort::ID_DESC, $default);
    }

    public function testChargeSortIncludesScheduledAt(): void
    {
        $values = array_column(ChargeSort::cases(), 'value');
        $this->assertContains('scheduled_at-asc', $values);
        $this->assertContains('scheduled_at-desc', $values);
    }

    public function testOrderSortEnum(): void
    {
        $this->assertEquals('id-desc', OrderSort::ID_DESC->value);
        $this->assertEquals('shipped_date-asc', OrderSort::SHIPPED_DATE_ASC->value);
        $this->assertEquals('shipping_date-desc', OrderSort::SHIPPING_DATE_DESC->value);
    }

    public function testOrderSortDefault(): void
    {
        $default = OrderSort::default();
        $this->assertEquals(OrderSort::ID_DESC, $default);
    }

    public function testOrderSortIncludesShippedDate(): void
    {
        $values = array_column(OrderSort::cases(), 'value');
        $this->assertContains('shipped_date-asc', $values);
        $this->assertContains('shipped_date-desc', $values);
    }

    public function testOrderSortIncludesShippingDate(): void
    {
        $values = array_column(OrderSort::cases(), 'value');
        $this->assertContains('shipping_date-asc', $values);
        $this->assertContains('shipping_date-desc', $values);
    }

    public function testCustomerSortEnum(): void
    {
        $this->assertEquals('id-desc', CustomerSort::ID_DESC->value);
        $this->assertEquals('created_at-asc', CustomerSort::CREATED_AT_ASC->value);
    }

    public function testCustomerSortDefault(): void
    {
        $default = CustomerSort::default();
        $this->assertEquals(CustomerSort::ID_DESC, $default);
    }

    public function testCustomerSortDoesNotIncludeScheduledAt(): void
    {
        $values = array_column(CustomerSort::cases(), 'value');
        $this->assertNotContains('scheduled_at-asc', $values);
        $this->assertNotContains('scheduled_at-desc', $values);
    }

    public function testBundleSortEnum(): void
    {
        $this->assertEquals('id-desc', BundleSort::ID_DESC->value);
        $this->assertEquals('updated_at-asc', BundleSort::UPDATED_AT_ASC->value);
    }

    public function testBundleSortDefault(): void
    {
        $default = BundleSort::default();
        $this->assertEquals(BundleSort::ID_DESC, $default);
    }

    public function testMetafieldSortEnum(): void
    {
        $this->assertEquals('id-desc', MetafieldSort::ID_DESC->value);
        $this->assertEquals('updated_at-asc', MetafieldSort::UPDATED_AT_ASC->value);
        $this->assertEquals('updated_at-desc', MetafieldSort::UPDATED_AT_DESC->value);
    }

    public function testMetafieldSortDoesNotIncludeCreatedAt(): void
    {
        $values = array_column(MetafieldSort::cases(), 'value');
        $this->assertNotContains('created_at-asc', $values);
        $this->assertNotContains('created_at-desc', $values);
    }

    public function testMetafieldSortDefault(): void
    {
        $default = MetafieldSort::default();
        $this->assertEquals(MetafieldSort::ID_DESC, $default);
    }

    public function testMetafieldSortTryFromString(): void
    {
        $this->assertEquals(MetafieldSort::ID_DESC, MetafieldSort::tryFromString('id-desc'));
        $this->assertEquals(MetafieldSort::UPDATED_AT_ASC, MetafieldSort::tryFromString('updated_at-asc'));
        $this->assertNull(MetafieldSort::tryFromString('created_at-asc'));
        $this->assertNull(MetafieldSort::tryFromString('invalid-sort'));
    }

    public function testOneTimeSortEnum(): void
    {
        $this->assertEquals('id-desc', OneTimeSort::ID_DESC->value);
        $this->assertEquals('created_at-asc', OneTimeSort::CREATED_AT_ASC->value);
        $this->assertEquals('updated_at-desc', OneTimeSort::UPDATED_AT_DESC->value);
    }

    public function testOneTimeSortDefault(): void
    {
        $default = OneTimeSort::default();
        $this->assertEquals(OneTimeSort::ID_DESC, $default);
    }

    public function testOneTimeSortTryFromString(): void
    {
        $this->assertEquals(OneTimeSort::ID_DESC, OneTimeSort::tryFromString('id-desc'));
        $this->assertEquals(OneTimeSort::CREATED_AT_ASC, OneTimeSort::tryFromString('created_at-asc'));
        $this->assertEquals(OneTimeSort::UPDATED_AT_DESC, OneTimeSort::tryFromString('updated_at-desc'));
        $this->assertNull(OneTimeSort::tryFromString('invalid-sort'));
    }
}
