<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Data\Address;
use Recharge\Data\Charge;
use Recharge\Data\Customer;
use Recharge\Data\Order;
use Recharge\Data\Subscription;
use Recharge\Enums\ChargeStatus;
use Recharge\Enums\OrderStatus;
use Recharge\Enums\SubscriptionStatus;

/**
 * Tests for Data Transfer Objects (DTOs)
 *
 * Verifies DTOs correctly parse responses from both API versions (2021-01 and 2021-11)
 * and handle version-specific fields appropriately.
 */
class DTOTest extends TestCase
{
    // Subscription Tests

    public function testSubscriptionParsesVersion2021_01Response(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'address_id' => 789,
            'product_title' => 'Coffee Subscription', // Old field name
            'status' => 'ACTIVE', // Uppercase in 2021-01
            'quantity' => 1,
            'price' => '29.99',
            'order_interval_unit' => 'month',
            'order_interval_frequency' => 1,
            'shipping_date' => '2024-01-15', // Old field name
            'shopify_variant_id' => 12345,
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-10T00:00:00Z',
        ];

        $subscription = Subscription::fromArray($data);

        $this->assertEquals(123, $subscription->id);
        $this->assertEquals(456, $subscription->customerId);
        $this->assertEquals('Coffee Subscription', $subscription->productTitle);
        $this->assertNull($subscription->title); // Not in 2021-01
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status);
        $this->assertNotNull($subscription->shippingDate);
        $this->assertNull($subscription->nextChargeScheduledAt); // Not in this response
        $this->assertEquals(12345, $subscription->shopifyVariantId);
    }

    public function testSubscriptionParsesVersion2021_11Response(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'title' => 'Coffee Subscription', // New field name
            'status' => 'active', // Lowercase in 2021-11
            'quantity' => 1,
            'price' => '29.99',
            'order_interval_unit' => 'month',
            'order_interval_frequency' => 1,
            'scheduled_at' => '2024-01-15T00:00:00Z', // New field name
            'plan_id' => 999, // 2021-11 only
            'external_product_id' => ['ecommerce' => '12345'],
            'external_variant_id' => ['ecommerce' => '67890'],
            'presentment_currency' => 'USD', // 2021-11 only
            'is_prepaid' => false,
            'is_skippable' => true,
            'is_swappable' => true,
            'analytics_data' => ['source' => 'web'],
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $subscription = Subscription::fromArray($data);

        $this->assertEquals(123, $subscription->id);
        $this->assertEquals('Coffee Subscription', $subscription->title);
        $this->assertNull($subscription->productTitle); // Not in 2021-11
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status); // Parsed from lowercase
        $this->assertNotNull($subscription->nextChargeScheduledAt);
        $this->assertEquals(999, $subscription->planId);
        $this->assertEquals('USD', $subscription->presentmentCurrency);
        $this->assertTrue($subscription->isSkippable);
        $this->assertIsArray($subscription->externalProductId);
    }

    public function testSubscriptionHelperMethodsWorkWithBothVersions(): void
    {
        // 2021-01 style
        $data2021 = [
            'id' => 1,
            'customer_id' => 1,
            'product_title' => 'Product A',
            'shipping_date' => '2024-01-15',
        ];

        $sub2021 = Subscription::fromArray($data2021);
        $this->assertEquals('Product A', $sub2021->getProductTitle());
        $this->assertNotNull($sub2021->getScheduledAt());

        // 2021-11 style
        $data2021_11 = [
            'id' => 2,
            'customer_id' => 2,
            'title' => 'Product B',
            'scheduled_at' => '2024-01-20T00:00:00Z',
        ];

        $sub2021_11 = Subscription::fromArray($data2021_11);
        $this->assertEquals('Product B', $sub2021_11->getProductTitle());
        $this->assertNotNull($sub2021_11->getScheduledAt());
    }

    // Customer Tests

    public function testCustomerParsesVersion2021_01Response(): void
    {
        $data = [
            'id' => 123,
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '555-1234',
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $customer = Customer::fromArray($data);

        $this->assertEquals(123, $customer->id);
        $this->assertEquals('test@example.com', $customer->email);
        $this->assertEquals('John Doe', $customer->getFullName());
        $this->assertNull($customer->taxExempt); // Not in 2021-01
    }

    public function testCustomerParsesVersion2021_11Response(): void
    {
        $data = [
            'id' => 123,
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tax_exempt' => true, // 2021-11 only
            'has_valid_payment_method' => true,
            'has_payment_method_in_dunning' => false,
            'subscriptions_active_count' => 3,
            'external_customer_id' => ['ecommerce' => 'cust_123'],
            'analytics_data' => ['source' => 'mobile'],
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $customer = Customer::fromArray($data);

        $this->assertEquals(123, $customer->id);
        $this->assertTrue($customer->taxExempt);
        $this->assertTrue($customer->hasValidPaymentMethod);
        $this->assertEquals(3, $customer->subscriptionsActiveCount);
        $this->assertIsArray($customer->externalCustomerId);
        $this->assertIsArray($customer->analyticsData);
    }

    // Address Tests

    public function testAddressParsesVersion2021_11ResponseWithNewFields(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'address1' => '123 Main St',
            'city' => 'New York',
            'country' => 'United States',
            'country_code' => 'US', // 2021-11 only
            'payment_method_id' => 789, // 2021-11 only
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $address = Address::fromArray($data);

        $this->assertEquals(123, $address->id);
        $this->assertEquals('US', $address->countryCode);
        $this->assertEquals(789, $address->paymentMethodId);
    }

    // Charge Tests

    public function testChargeParsesWithStatus(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'amount' => '29.99',
            'status' => 'SUCCESS', // Uppercase
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $charge = Charge::fromArray($data);

        $this->assertNotNull($charge->status);
        $this->assertEquals(ChargeStatus::SUCCESS, $charge->status);
        $this->assertTrue($charge->status->isSuccess());
    }

    public function testChargeParsesVersion2021_11ResponseWithCountryCodes(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'amount' => '29.99',
            'status' => 'success', // Lowercase
            'billing_address_country_code' => 'US', // 2021-11 only
            'shipping_address_country_code' => 'US',
            'orders_count' => 1,
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $charge = Charge::fromArray($data);

        $this->assertEquals(ChargeStatus::SUCCESS, $charge->status);
        $this->assertEquals('US', $charge->billingAddressCountryCode);
        $this->assertEquals('US', $charge->shippingAddressCountryCode);
        $this->assertEquals(1, $charge->ordersCount);
    }

    // Order Tests

    public function testOrderParsesWithStatus(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'order_number' => 'ORD-123',
            'status' => 'QUEUED',
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $order = Order::fromArray($data);

        $this->assertEquals(OrderStatus::QUEUED, $order->status);
    }

    public function testOrderParsesVersion2021_11ResponseWithCountryCodes(): void
    {
        $data = [
            'id' => 123,
            'customer_id' => 456,
            'billing_address_country_code' => 'CA', // 2021-11 only
            'shipping_address_country_code' => 'CA',
            'created_at' => '2024-01-01T00:00:00Z',
        ];

        $order = Order::fromArray($data);

        $this->assertEquals('CA', $order->billingAddressCountryCode);
        $this->assertEquals('CA', $order->shippingAddressCountryCode);
    }

    // Status Case-Insensitive Tests

    public function testStatusEnumsParseCaseInsensitively(): void
    {
        // Subscription status
        $subUppercase = Subscription::fromArray([
            'id' => 1,
            'customer_id' => 1,
            'status' => 'ACTIVE', // 2021-01 uppercase
        ]);
        $subLowercase = Subscription::fromArray([
            'id' => 2,
            'customer_id' => 2,
            'status' => 'active', // 2021-11 lowercase
        ]);

        $this->assertEquals(SubscriptionStatus::ACTIVE, $subUppercase->status);
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subLowercase->status);

        // Charge status
        $chargeUppercase = Charge::fromArray([
            'id' => 1,
            'customer_id' => 1,
            'status' => 'SUCCESS',
        ]);
        $chargeLowercase = Charge::fromArray([
            'id' => 2,
            'customer_id' => 2,
            'status' => 'success',
        ]);

        $this->assertEquals(ChargeStatus::SUCCESS, $chargeUppercase->status);
        $this->assertEquals(ChargeStatus::SUCCESS, $chargeLowercase->status);
    }

    // toArray Tests

    public function testDTOsFilterNullValuesInToArray(): void
    {
        $subscription = Subscription::fromArray([
            'id' => 123,
            'customer_id' => 456,
            'title' => 'Test',
        ]);

        $array = $subscription->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('customer_id', $array);
        $this->assertArrayHasKey('title', $array);
        // Null values should be filtered out
        $this->assertArrayNotHasKey('address_id', $array);
    }
}
