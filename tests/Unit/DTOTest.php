<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Data\Address;
use Recharge\Data\Bundle;
use Recharge\Data\Charge;
use Recharge\Data\Checkout;
use Recharge\Data\Collection;
use Recharge\Data\Credit;
use Recharge\Data\Customer;
use Recharge\Data\Discount;
use Recharge\Data\Metafield;
use Recharge\Data\Order;
use Recharge\Data\Subscription;
use Recharge\Enums\AppliesToProductType;
use Recharge\Enums\ChargeStatus;
use Recharge\Enums\CollectionSortOrder;
use Recharge\Enums\DiscountDuration;
use Recharge\Enums\DiscountStatus;
use Recharge\Enums\DiscountType;
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

    // Discount Tests

    public function testDiscountParsesVersion2021_01Response(): void
    {
        $data = [
            'id' => 123,
            'code' => 'SAVE10',
            'discount_type' => 'percentage', // 2021-01 uses discount_type
            'value' => 10,
            'status' => 'enabled',
            'duration' => 'forever',
            'usage_limit' => 100,
            'times_used' => 5,
            'applies_to_product_type' => 'ALL',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-10T00:00:00Z',
        ];

        $discount = Discount::fromArray($data);

        $this->assertEquals(123, $discount->id);
        $this->assertEquals('SAVE10', $discount->code);
        $this->assertEquals(DiscountType::PERCENTAGE, $discount->discountType);
        $this->assertEquals(10.0, $discount->value);
        $this->assertEquals(DiscountStatus::ENABLED, $discount->status);
        $this->assertEquals(DiscountDuration::FOREVER, $discount->duration);
        $this->assertEquals(100, $discount->usageLimit);
        $this->assertEquals(5, $discount->timesUsed);
        $this->assertEquals(AppliesToProductType::ALL, $discount->appliesToProductType);
    }

    public function testDiscountParsesVersion2021_11Response(): void
    {
        $data = [
            'id' => 456,
            'code' => 'FIXED20',
            'value_type' => 'fixed_amount', // 2021-11 uses value_type
            'value' => 20,
            'status' => 'enabled',
            'duration' => 'usage_limit',
            'usage_limit' => 50,
            'times_used' => 10,
            'applies_to_product_type' => 'SUBSCRIPTION',
            'channel_settings' => [
                'api' => ['can_apply' => true],
                'checkout_page' => ['can_apply' => true],
            ],
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-10T00:00:00Z',
        ];

        $discount = Discount::fromArray($data);

        $this->assertEquals(456, $discount->id);
        $this->assertEquals('FIXED20', $discount->code);
        $this->assertEquals(DiscountType::FIXED_AMOUNT, $discount->discountType);
        $this->assertEquals(20.0, $discount->value);
        $this->assertEquals(DiscountDuration::USAGE_LIMIT, $discount->duration);
        $this->assertEquals(50, $discount->usageLimit);
        $this->assertEquals(10, $discount->timesUsed);
        $this->assertEquals(AppliesToProductType::SUBSCRIPTION, $discount->appliesToProductType);
        $this->assertIsArray($discount->channelSettings);
        $this->assertArrayHasKey('api', $discount->channelSettings);
    }

    public function testDiscountIsActiveHelper(): void
    {
        $now = new \DateTimeImmutable();
        $future = $now->modify('+1 day');
        $past = $now->modify('-1 day');

        // Active discount
        $active = Discount::fromArray([
            'id' => 1,
            'status' => 'enabled',
            'starts_at' => $past->format('Y-m-d\TH:i:s\Z'),
            'ends_at' => $future->format('Y-m-d\TH:i:s\Z'),
        ]);
        $this->assertTrue($active->isActive());

        // Disabled discount
        $disabled = Discount::fromArray([
            'id' => 2,
            'status' => 'disabled',
        ]);
        $this->assertFalse($disabled->isActive());

        // Expired discount
        $expired = Discount::fromArray([
            'id' => 3,
            'status' => 'enabled',
            'ends_at' => $past->format('Y-m-d\TH:i:s\Z'),
        ]);
        $this->assertFalse($expired->isActive());
    }

    public function testDiscountHasReachedUsageLimitHelper(): void
    {
        // Has reached limit
        $limited = Discount::fromArray([
            'id' => 1,
            'usage_limit' => 10,
            'times_used' => 10,
        ]);
        $this->assertTrue($limited->hasReachedUsageLimit());

        // Has not reached limit
        $notLimited = Discount::fromArray([
            'id' => 2,
            'usage_limit' => 10,
            'times_used' => 5,
        ]);
        $this->assertFalse($notLimited->hasReachedUsageLimit());

        // No limit set
        $noLimit = Discount::fromArray([
            'id' => 3,
            'times_used' => 100,
        ]);
        $this->assertFalse($noLimit->hasReachedUsageLimit());
    }

    // Bundle Tests
    public function testBundleParsesBasicResponse(): void
    {
        $data = [
            'id' => 1,
            'bundle_variant_id' => 123,
            'purchase_item_id' => 456,
            'external_product_id' => ['ecommerce' => 'shopify', 'product_id' => '789'],
            'external_variant_id' => ['ecommerce' => 'shopify', 'variant_id' => '101'],
            'items' => [
                [
                    'id' => 1,
                    'collection_id' => 10,
                    'quantity' => 2,
                ],
            ],
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $bundle = Bundle::fromArray($data);

        $this->assertEquals(1, $bundle->id);
        $this->assertEquals(123, $bundle->bundleVariantId);
        $this->assertEquals(456, $bundle->purchaseItemId);
        $this->assertIsArray($bundle->externalProductId);
        $this->assertIsArray($bundle->externalVariantId);
        $this->assertIsArray($bundle->items);
        $this->assertCount(1, $bundle->items);
        $this->assertNotNull($bundle->createdAt);
        $this->assertNotNull($bundle->updatedAt);
    }

    public function testBundleHandlesNullFields(): void
    {
        $data = [
            'id' => 2,
        ];

        $bundle = Bundle::fromArray($data);

        $this->assertEquals(2, $bundle->id);
        $this->assertNull($bundle->bundleVariantId);
        $this->assertNull($bundle->purchaseItemId);
        $this->assertNull($bundle->externalProductId);
        $this->assertNull($bundle->externalVariantId);
        $this->assertNull($bundle->items);
    }

    // Checkout Tests
    public function testCheckoutParsesBasicResponse(): void
    {
        $data = [
            'token' => 'checkout_token_123',
            'email' => 'customer@example.com',
            'currency' => 'USD',
            'line_items' => [
                [
                    'id' => 1,
                    'quantity' => 2,
                ],
            ],
            'billing_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ],
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $checkout = Checkout::fromArray($data);

        $this->assertEquals('checkout_token_123', $checkout->token);
        $this->assertEquals('customer@example.com', $checkout->email);
        $this->assertEquals('USD', $checkout->currency);
        $this->assertIsArray($checkout->lineItems);
        $this->assertIsArray($checkout->billingAddress);
        $this->assertNotNull($checkout->createdAt);
        $this->assertNotNull($checkout->updatedAt);
    }

    public function testCheckoutHandlesNullFields(): void
    {
        $data = [
            'token' => 'checkout_token_456',
        ];

        $checkout = Checkout::fromArray($data);

        $this->assertEquals('checkout_token_456', $checkout->token);
        $this->assertNull($checkout->email);
        $this->assertNull($checkout->lineItems);
        $this->assertNull($checkout->billingAddress);
        $this->assertNull($checkout->chargeId);
    }

    public function testCheckoutParsesAfterProcessing(): void
    {
        $data = [
            'token' => 'checkout_token_789',
            'charge_id' => 12345,
            'completed_at' => '2024-01-01T12:00:00Z',
        ];

        $checkout = Checkout::fromArray($data);

        $this->assertEquals('checkout_token_789', $checkout->token);
        $this->assertEquals(12345, $checkout->chargeId);
        $this->assertNotNull($checkout->completedAt);
    }

    // Collection Tests
    public function testCollectionParsesBasicResponse(): void
    {
        $data = [
            'id' => 1,
            'title' => 'Featured Products',
            'description' => 'Our featured product collection',
            'type' => 'manual',
            'sort_order' => 'title-asc',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $collection = Collection::fromArray($data);

        $this->assertEquals(1, $collection->id);
        $this->assertEquals('Featured Products', $collection->title);
        $this->assertEquals('Our featured product collection', $collection->description);
        $this->assertEquals('manual', $collection->type);
        $this->assertEquals(CollectionSortOrder::TITLE_ASC, $collection->sortOrder);
        $this->assertNotNull($collection->createdAt);
        $this->assertNotNull($collection->updatedAt);
    }

    public function testCollectionHandlesNullFields(): void
    {
        $data = [
            'id' => 2,
        ];

        $collection = Collection::fromArray($data);

        $this->assertEquals(2, $collection->id);
        $this->assertNull($collection->title);
        $this->assertNull($collection->description);
        $this->assertNull($collection->type);
        $this->assertNull($collection->sortOrder);
    }

    // Credit Tests
    public function testCreditParsesBasicResponse(): void
    {
        $data = [
            'id' => 1,
            'customer_id' => 123,
            'amount' => '25.00',
            'currency' => 'USD',
            'note' => 'Promotional credit',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $credit = Credit::fromArray($data);

        $this->assertEquals(1, $credit->id);
        $this->assertEquals(123, $credit->customerId);
        $this->assertEquals('25.00', $credit->amount);
        $this->assertEquals('USD', $credit->currency);
        $this->assertEquals('Promotional credit', $credit->note);
        $this->assertNotNull($credit->createdAt);
        $this->assertNotNull($credit->updatedAt);
    }

    public function testCreditHandlesFloatAmount(): void
    {
        $data = [
            'id' => 2,
            'customer_id' => 456,
            'amount' => 50.50,
            'currency' => 'USD',
        ];

        $credit = Credit::fromArray($data);

        $this->assertEquals(2, $credit->id);
        $this->assertEquals(456, $credit->customerId);
        $this->assertEquals(50.50, $credit->amount);
        $this->assertEquals('USD', $credit->currency);
    }

    public function testCreditHandlesNullFields(): void
    {
        $data = [
            'id' => 3,
            'customer_id' => 789,
        ];

        $credit = Credit::fromArray($data);

        $this->assertEquals(3, $credit->id);
        $this->assertEquals(789, $credit->customerId);
        $this->assertNull($credit->amount);
        $this->assertNull($credit->currency);
        $this->assertNull($credit->note);
        $this->assertNull($credit->createdAt);
        $this->assertNull($credit->updatedAt);
    }

    public function testCreditToArray(): void
    {
        $data = [
            'id' => 1,
            'customer_id' => 123,
            'amount' => '25.00',
            'currency' => 'USD',
            'note' => 'Promotional credit',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $credit = Credit::fromArray($data);
        $array = $credit->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals(123, $array['customer_id']);
        $this->assertEquals('25.00', $array['amount']);
        $this->assertEquals('USD', $array['currency']);
        $this->assertEquals('Promotional credit', $array['note']);
        $this->assertStringContainsString('2024-01-01', $array['created_at']);
        $this->assertStringContainsString('2024-01-01', $array['updated_at']);
    }

    // Metafield Tests
    public function testMetafieldParsesBasicResponse(): void
    {
        $data = [
            'id' => 1,
            'owner_resource' => 'customer',
            'owner_id' => 123,
            'namespace' => 'custom',
            'key' => 'preferred_language',
            'value' => 'en',
            'type' => 'single_line_text_field',
            'description' => 'Customer preferred language',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $metafield = Metafield::fromArray($data);

        $this->assertEquals(1, $metafield->id);
        $this->assertEquals('customer', $metafield->ownerResource);
        $this->assertEquals(123, $metafield->ownerId);
        $this->assertEquals('custom', $metafield->namespace);
        $this->assertEquals('preferred_language', $metafield->key);
        $this->assertEquals('en', $metafield->value);
        $this->assertEquals('single_line_text_field', $metafield->type);
        $this->assertEquals('Customer preferred language', $metafield->description);
        $this->assertNotNull($metafield->createdAt);
        $this->assertNotNull($metafield->updatedAt);
    }

    public function testMetafieldHandlesNumericValue(): void
    {
        $data = [
            'id' => 2,
            'owner_resource' => 'subscription',
            'owner_id' => 456,
            'namespace' => 'custom',
            'key' => 'priority',
            'value' => 5,
            'type' => 'number_integer',
        ];

        $metafield = Metafield::fromArray($data);

        $this->assertEquals(2, $metafield->id);
        $this->assertEquals('subscription', $metafield->ownerResource);
        $this->assertEquals(456, $metafield->ownerId);
        $this->assertEquals(5, $metafield->value);
        $this->assertEquals('number_integer', $metafield->type);
    }

    public function testMetafieldHandlesJsonStringValue(): void
    {
        $data = [
            'id' => 3,
            'owner_resource' => 'charge',
            'owner_id' => 789,
            'namespace' => 'custom',
            'key' => 'metadata',
            'value' => '{"source":"api","version":"1.0"}',
            'type' => 'json',
        ];

        $metafield = Metafield::fromArray($data);

        $this->assertEquals(3, $metafield->id);
        $this->assertIsArray($metafield->value);
        $this->assertEquals('api', $metafield->value['source']);
        $this->assertEquals('1.0', $metafield->value['version']);
    }

    public function testMetafieldHandlesNullFields(): void
    {
        $data = [
            'id' => 4,
            'owner_resource' => 'customer',
            'owner_id' => 999,
            'namespace' => 'custom',
            'key' => 'notes',
        ];

        $metafield = Metafield::fromArray($data);

        $this->assertEquals(4, $metafield->id);
        $this->assertNull($metafield->value);
        $this->assertNull($metafield->type);
        $this->assertNull($metafield->description);
        $this->assertNull($metafield->createdAt);
        $this->assertNull($metafield->updatedAt);
    }

    public function testMetafieldToArray(): void
    {
        $data = [
            'id' => 1,
            'owner_resource' => 'customer',
            'owner_id' => 123,
            'namespace' => 'custom',
            'key' => 'preferred_language',
            'value' => 'en',
            'type' => 'single_line_text_field',
            'description' => 'Customer preferred language',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T00:00:00Z',
        ];

        $metafield = Metafield::fromArray($data);
        $array = $metafield->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertEquals('customer', $array['owner_resource']);
        $this->assertEquals(123, $array['owner_id']);
        $this->assertEquals('custom', $array['namespace']);
        $this->assertEquals('preferred_language', $array['key']);
        $this->assertEquals('en', $array['value']);
        $this->assertEquals('single_line_text_field', $array['type']);
        $this->assertStringContainsString('2024-01-01', $array['created_at']);
        $this->assertStringContainsString('2024-01-01', $array['updated_at']);
    }

    public function testMetafieldToArrayWithJsonValue(): void
    {
        $metafield = new Metafield(
            id: 1,
            ownerResource: 'customer',
            ownerId: 123,
            namespace: 'custom',
            key: 'metadata',
            value: ['source' => 'api', 'version' => '1.0'],
            type: 'json'
        );

        $array = $metafield->toArray();

        $this->assertEquals(1, $array['id']);
        $this->assertIsString($array['value']);
        $decoded = json_decode($array['value'], true);
        $this->assertEquals('api', $decoded['source']);
        $this->assertEquals('1.0', $decoded['version']);
    }
}
