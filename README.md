# Recharge API PHP SDK

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Modern, type-safe PHP SDK for the [Recharge Payments API](https://developer.rechargepayments.com/).

## Features

- 🚀 **Simple API** - Clean, intuitive interface
- 🔄 **Auto Pagination** - Handles cursor pagination automatically
- 🔒 **Type-Safe** - Full type hints with PHP 8.2+ enums
- ✅ **Multi-Version** - Supports API versions 2021-01 and 2021-11
- 📦 **PSR Compliant** - Follows PHP standards (PSR-3, PSR-7, PSR-12, PSR-18)

## Installation

```bash
composer require vendor/recharge-api
```

## Quick Start

```php
use Recharge\RechargeClient;

$client = new RechargeClient('your-api-token');

// List subscriptions
foreach ($client->subscriptions()->list() as $subscription) {
    echo $subscription->id . "\n";
}
```

## Usage

### List Resources

```php
// Subscriptions
foreach ($client->subscriptions()->list() as $sub) {
    echo $sub->id . " - " . $sub->getProductTitle() . "\n";
}

// Customers
foreach ($client->customers()->list() as $customer) {
    echo $customer->email . "\n";
}

// With filters
foreach ($client->subscriptions()->list(['status' => 'ACTIVE']) as $sub) {
    // Process active subscriptions
}

// With sorting (using enum - recommended)
use Recharge\Enums\Sort\SubscriptionSort;

foreach ($client->subscriptions()->list(['sort_by' => SubscriptionSort::CREATED_AT_DESC]) as $sub) {
    // Subscriptions sorted by creation date (newest first)
}

// With sorting (using string - also supported)
foreach ($client->subscriptions()->list(['sort_by' => 'created_at-desc']) as $sub) {
    // Subscriptions sorted by creation date (newest first)
}
```

### Sorting

The SDK supports sorting for list operations using type-safe enums or strings. Using enums is recommended for better IDE support and type safety.

**Available Sort Enums:**
- `SubscriptionSort` - For subscriptions
- `ChargeSort` - For charges
- `OrderSort` - For orders
- `CustomerSort` - For customers
- `DiscountSort` - For discounts
- `BundleSort` - For bundles
- `MetafieldSort` - For metafields
- `OneTimeSort` - For one-times
- `ProductSort` - For products
- `PaymentMethodSort` - For payment methods
- `PlanSort` - For plans (2021-11 only)
- `WebhookSort` - For webhooks

**Subscriptions (`SubscriptionSort`):**
- `SubscriptionSort::ID_ASC`, `SubscriptionSort::ID_DESC` (default)
- `SubscriptionSort::CREATED_AT_ASC`, `SubscriptionSort::CREATED_AT_DESC`
- `SubscriptionSort::UPDATED_AT_ASC`, `SubscriptionSort::UPDATED_AT_DESC`

**Charges (`ChargeSort`):**
- `ChargeSort::ID_ASC`, `ChargeSort::ID_DESC` (default)
- `ChargeSort::CREATED_AT_ASC`, `ChargeSort::CREATED_AT_DESC`
- `ChargeSort::UPDATED_AT_ASC`, `ChargeSort::UPDATED_AT_DESC`
- `ChargeSort::SCHEDULED_AT_ASC`, `ChargeSort::SCHEDULED_AT_DESC`

**Orders (`OrderSort`):**
- `OrderSort::ID_ASC`, `OrderSort::ID_DESC` (default)
- `OrderSort::CREATED_AT_ASC`, `OrderSort::CREATED_AT_DESC`
- `OrderSort::UPDATED_AT_ASC`, `OrderSort::UPDATED_AT_DESC`
- `OrderSort::SHIPPED_DATE_ASC`, `OrderSort::SHIPPED_DATE_DESC`
- `OrderSort::SHIPPING_DATE_ASC`, `OrderSort::SHIPPING_DATE_DESC` (deprecated)

**Customers (`CustomerSort`):**
- `CustomerSort::ID_ASC`, `CustomerSort::ID_DESC` (default)
- `CustomerSort::CREATED_AT_ASC`, `CustomerSort::CREATED_AT_DESC`
- `CustomerSort::UPDATED_AT_ASC`, `CustomerSort::UPDATED_AT_DESC`

**Discounts (`DiscountSort`):**
- `DiscountSort::ID_ASC`, `DiscountSort::ID_DESC` (default)
- `DiscountSort::CREATED_AT_ASC`, `DiscountSort::CREATED_AT_DESC`
- `DiscountSort::UPDATED_AT_ASC`, `DiscountSort::UPDATED_AT_DESC`

**Bundles (`BundleSort`):**
- `BundleSort::ID_ASC`, `BundleSort::ID_DESC` (default)
- `BundleSort::UPDATED_AT_ASC`, `BundleSort::UPDATED_AT_DESC`

**Metafields (`MetafieldSort`):**
- `MetafieldSort::ID_ASC`, `MetafieldSort::ID_DESC` (default)
- `MetafieldSort::UPDATED_AT_ASC`, `MetafieldSort::UPDATED_AT_DESC`

**One-Times (`OneTimeSort`):**
- `OneTimeSort::ID_ASC`, `OneTimeSort::ID_DESC` (default)
- `OneTimeSort::CREATED_AT_ASC`, `OneTimeSort::CREATED_AT_DESC`
- `OneTimeSort::UPDATED_AT_ASC`, `OneTimeSort::UPDATED_AT_DESC`

**Products (`ProductSort`):**
- `ProductSort::ID_ASC`, `ProductSort::ID_DESC` (default)
- `ProductSort::CREATED_AT_ASC`, `ProductSort::CREATED_AT_DESC`
- `ProductSort::UPDATED_AT_ASC`, `ProductSort::UPDATED_AT_DESC`
- `ProductSort::TITLE_ASC`, `ProductSort::TITLE_DESC`

**Payment Methods (`PaymentMethodSort`):**
- `PaymentMethodSort::ID_ASC`, `PaymentMethodSort::ID_DESC` (default)
- `PaymentMethodSort::CREATED_AT_ASC`, `PaymentMethodSort::CREATED_AT_DESC`
- `PaymentMethodSort::UPDATED_AT_ASC`, `PaymentMethodSort::UPDATED_AT_DESC`

**Plans (`PlanSort`):**
- `PlanSort::ID_ASC`, `PlanSort::ID_DESC` (default)
- `PlanSort::CREATED_AT_ASC`, `PlanSort::CREATED_AT_DESC`
- `PlanSort::UPDATED_AT_ASC`, `PlanSort::UPDATED_AT_DESC`
- Note: Plans are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.

**Webhooks (`WebhookSort`):**
- `WebhookSort::ID_ASC`, `WebhookSort::ID_DESC` (default)
- `WebhookSort::CREATED_AT_ASC`, `WebhookSort::CREATED_AT_DESC`
- `WebhookSort::UPDATED_AT_ASC`, `WebhookSort::UPDATED_AT_DESC`

```php
use Recharge\Enums\Sort\SubscriptionSort;
use Recharge\Enums\Sort\ChargeSort;

// Using enums (recommended)
foreach ($client->subscriptions()->list(['sort_by' => SubscriptionSort::CREATED_AT_DESC]) as $sub) {
    // ...
}

// Combine sorting with filters
foreach ($client->charges()->list([
    'status' => 'queued',
    'sort_by' => ChargeSort::SCHEDULED_AT_ASC
]) as $charge) {
    // Queued charges sorted by scheduled date (earliest first)
}

// String values also work (for backward compatibility)
foreach ($client->subscriptions()->list(['sort_by' => 'created_at-desc']) as $sub) {
    // ...
}

// Discounts sorting
use Recharge\Enums\Sort\DiscountSort;

foreach ($client->discounts()->list(['sort_by' => DiscountSort::CREATED_AT_DESC]) as $discount) {
    // Discounts sorted by creation date (newest first)
}
```

### Get Single Resource

```php
$subscription = $client->subscriptions()->get(123);
$customer = $client->customers()->get(456);
$order = $client->orders()->get(789);
```

### Get Count

```php
// Get count of subscriptions (requires API 2021-01, automatically handled)
$count = $client->subscriptions()->count(['status' => 'ACTIVE']);

// Get count of charges with filters
$queuedCount = $client->charges()->count(['status' => 'queued']);

// Note: Count endpoints are only available in API version 2021-01.
// The count() method automatically switches to 2021-01 for the request.
```

### Create Subscription

```php
$subscription = $client->subscriptions()->create([
    'customer_id' => 456,
    'quantity' => 2,
    'price' => 29.99,
    'order_interval_unit' => 'month',
    'order_interval_frequency' => 1,
]);
```

### Update & Delete

```php
// Update
$client->subscriptions()->update(123, [
    'quantity' => 3,
    'price' => 39.99,
]);

// Cancel
$client->subscriptions()->cancel(123, 'Customer requested');

// Delete
$client->subscriptions()->delete(123);
```

### Notifications

```php
// Send a notification to a customer
// Using enum (recommended)
use Recharge\Enums\NotificationTemplate;

$client->customers()->sendNotification(
    123,
    NotificationTemplate::GET_ACCOUNT_ACCESS
);

// Using string template name
$client->customers()->sendNotification(
    123,
    'upcoming_charge'
);

// With template variables (if required by template)
$client->customers()->sendNotification(
    123,
    NotificationTemplate::UPCOMING_CHARGE,
    [
        'charge_date' => '2024-12-31',
        'amount' => '29.99',
    ]
);
```

**Supported Templates:**
- `NotificationTemplate::GET_ACCOUNT_ACCESS` - Send account access link/code
- `NotificationTemplate::UPCOMING_CHARGE` - Send notification about upcoming charge

Note: Both templates are supported in API versions 2021-01 and 2021-11.

### Discounts

```php
// List discounts
foreach ($client->discounts()->list() as $discount) {
    echo $discount->code . " - " . $discount->value . "\n";
}

// Get a discount
$discount = $client->discounts()->get(123);

// Create a discount
$discount = $client->discounts()->create([
    'code' => 'SAVE10',
    'discount_type' => 'percentage', // or 'value_type' in 2021-11
    'value' => 10,
    'duration' => 'forever',
    'status' => 'enabled',
]);

// Update a discount
$client->discounts()->update(123, ['value' => 15]);

// Delete a discount
$client->discounts()->delete(123);

// Get count of discounts (requires API 2021-01, automatically handled)
$count = $client->discounts()->count(['status' => 'enabled']);

// Apply discount to an address
$client->discounts()->applyToAddress(456, ['discount_code' => 'SAVE10']);

// Apply discount to a charge
$client->discounts()->applyToCharge(789, ['discount_code' => 'SAVE10']);

// Remove a discount
$client->discounts()->remove(['address_id' => 456]);
```

### Bundle Selections

```php
// List bundle selections
foreach ($client->bundles()->list() as $bundle) {
    echo "Bundle Selection ID: {$bundle->id}\n";
}

// Get a bundle selection
$bundle = $client->bundles()->get(123);

// Create a bundle selection
$bundle = $client->bundles()->create([
    'bundle_variant_id' => 456,
    'purchase_item_id' => 789,
]);

// Update a bundle selection
$client->bundles()->update(123, ['purchase_item_id' => 999]);

// Delete a bundle selection
$client->bundles()->delete(123);

// With sorting
use Recharge\Enums\Sort\BundleSort;

foreach ($client->bundles()->list(['sort_by' => BundleSort::UPDATED_AT_DESC]) as $bundle) {
    // Bundle selections sorted by update date (newest first)
}
```

### Charges

```php
// List charges
foreach ($client->charges()->list(['status' => 'queued']) as $charge) {
    echo "Charge ID: {$charge->id}, Status: {$charge->status?->value}\n";
}

// Get a charge
$charge = $client->charges()->get(123);

// Get count of charges
$queuedCount = $client->charges()->count(['status' => 'queued']);

// Apply discount to a charge
$client->charges()->applyDiscount(123, ['discount_code' => 'SAVE10']);

// Remove discount from a charge
$client->charges()->removeDiscount(123);

// Skip a charge
$client->charges()->skip(123);

// Unskip a charge
$client->charges()->unskip(123);

// Process a charge (Pro merchants only)
$client->charges()->process(123);

// Refund a charge
$client->charges()->refund(123, ['amount' => 10.00]);

// Capture a charge (Pro merchants only)
$client->charges()->capture(123);

// Change next charge date
$client->charges()->changeNextChargeDate(123, ['scheduled_at' => '2024-12-31']);

// Add free gift (2021-11 only)
$client->charges()->addFreeGift(123, ['external_variant_id' => ['ecommerce' => 'shopify', 'variant_id' => '456']]);

// Remove free gift (2021-11 only)
$client->charges()->removeFreeGift(123, ['external_variant_id' => ['ecommerce' => 'shopify', 'variant_id' => '456']]);
```

### Checkouts

```php
// Note: Checkouts are only available for BigCommerce and Custom setups.
// Not supported for Shopify stores (deprecated as of October 18, 2024).
// Requires Pro or Custom plan.

// Create a checkout
$checkout = $client->checkouts()->create([
    'email' => 'customer@example.com',
    'line_items' => [
        [
            'external_product_id' => ['ecommerce' => 'bigcommerce', 'product_id' => '123'],
            'external_variant_id' => ['ecommerce' => 'bigcommerce', 'variant_id' => '456'],
            'quantity' => 2,
        ],
    ],
    'billing_address' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Main St',
        'city' => 'New York',
        'province' => 'NY',
        'zip' => '10001',
        'country' => 'United States',
        'country_code' => 'US',
    ],
]);

// Get a checkout
$checkout = $client->checkouts()->get('checkout_token_123');

// Update a checkout
$checkout = $client->checkouts()->update('checkout_token_123', [
    'shipping_address' => [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'address1' => '456 Oak Ave',
    ],
]);

// Get shipping rates
$shippingRates = $client->checkouts()->getShippingRates('checkout_token_123');

// Process/charge a checkout
$checkout = $client->checkouts()->charge('checkout_token_123', [
    'payment_method' => [
        'type' => 'credit_card',
        'gateway' => 'stripe',
    ],
]);
// After processing, $checkout->chargeId will be set
```

### Collections

```php
// List collections
foreach ($client->collections()->list() as $collection) {
    echo "Collection: {$collection->title}\n";
}

// Filter by title
foreach ($client->collections()->list(['title' => 'Featured']) as $collection) {
    // Collections with title containing 'Featured'
}

// Get a collection
$collection = $client->collections()->get(123);

// Create a collection
$collection = $client->collections()->create([
    'title' => 'Featured Products',
    'description' => 'Our featured product collection',
    'sort_order' => 'title-asc',
]);

// Update a collection
$client->collections()->update(123, [
    'title' => 'Updated Collection',
    'sort_order' => 'created_at-desc',
]);

// List products in a collection
foreach ($client->collections()->listProducts(123) as $product) {
    echo "Product: {$product->title}\n";
}

// Bulk delete products from a collection (limit 250 per request)
$client->collections()->deleteProductsBulk(123, [456, 789, 101]);
```

### Credits

```php
// List credits
foreach ($client->credits()->list() as $credit) {
    echo "Credit ID: {$credit->id}, Amount: {$credit->amount}\n";
}

// Filter by customer
foreach ($client->credits()->list(['customer_id' => 123]) as $credit) {
    // Credits for a specific customer
}

// Get a credit
$credit = $client->credits()->get(123);

// Create a credit
$credit = $client->credits()->create([
    'customer_id' => 123,
    'amount' => 25.00,
    'currency' => 'USD',
    'note' => 'Promotional credit',
]);

// Update a credit
$client->credits()->update(123, [
    'amount' => 50.00,
    'note' => 'Updated promotional credit',
]);

// Delete a credit
$client->credits()->delete(123);
```

### Metafields

```php
// List metafields
foreach ($client->metafields()->list() as $metafield) {
    echo "Metafield: {$metafield->namespace}.{$metafield->key} = {$metafield->value}\n";
}

// With sorting (using enum - recommended)
// Note: Sorting is only available in API version 2021-01 (automatically handled)
use Recharge\Enums\Sort\MetafieldSort;

foreach ($client->metafields()->list(['sort_by' => MetafieldSort::UPDATED_AT_DESC]) as $metafield) {
    // Metafields sorted by update date (newest first)
}

// Filter by owner resource
foreach ($client->metafields()->list(['owner_resource' => 'customer']) as $metafield) {
    // Metafields for customers only
}

// Filter by owner ID and namespace
foreach ($client->metafields()->list([
    'owner_resource' => 'subscription',
    'owner_id' => 123,
    'namespace' => 'custom',
]) as $metafield) {
    // Metafields for a specific subscription with custom namespace
}

// Get a metafield
$metafield = $client->metafields()->get(123);

// Create a metafield
$metafield = $client->metafields()->create([
    'owner_resource' => 'customer',
    'owner_id' => 123,
    'namespace' => 'custom',
    'key' => 'preferred_language',
    'value' => 'en',
    'type' => 'single_line_text_field',
    'description' => 'Customer preferred language',
]);

// Create a metafield with JSON value
$metafield = $client->metafields()->create([
    'owner_resource' => 'subscription',
    'owner_id' => 456,
    'namespace' => 'custom',
    'key' => 'metadata',
    'value' => json_encode(['source' => 'api', 'version' => '1.0']),
    'type' => 'json',
]);

// Update a metafield
$client->metafields()->update(123, [
    'value' => 'es',
    'description' => 'Updated to Spanish',
]);

// Delete a metafield
$client->metafields()->delete(123);
```

### One-Times

One-times are non-recurring line items attached to a QUEUED charge. They represent one-off purchases rather than subscriptions.

```php
// List one-times
foreach ($client->oneTimes()->list() as $onetime) {
    echo "One-Time ID: {$onetime->id}, Price: {$onetime->price}\n";
}

// With sorting (using enum - recommended)
use Recharge\Enums\Sort\OneTimeSort;

foreach ($client->oneTimes()->list(['sort_by' => OneTimeSort::CREATED_AT_DESC]) as $onetime) {
    // One-times sorted by creation date (newest first)
}

// Filter by address
foreach ($client->oneTimes()->list(['address_id' => 123]) as $onetime) {
    // One-times for a specific address
}

// Filter by customer
foreach ($client->oneTimes()->list(['customer_id' => 456]) as $onetime) {
    // One-times for a specific customer
}

// Filter by date range
foreach ($client->oneTimes()->list([
    'created_at_min' => '2024-01-01',
    'created_at_max' => '2024-12-31',
]) as $onetime) {
    // One-times created in 2024
}

// Get a one-time
$onetime = $client->oneTimes()->get(123);

// Create a one-time
// Note: In API version 2021-01, address_id must be in the path.
// In 2021-11, address_id can be in the request body.
// The SDK automatically handles version differences.
$onetime = $client->oneTimes()->create([
    'address_id' => 123, // Required
    'external_variant_id' => 'variant_456',
    'quantity' => 2,
    'price' => 29.99,
]);

// Update a one-time
$client->oneTimes()->update(123, [
    'quantity' => 3,
    'price' => 39.99,
]);

// Delete a one-time
$client->oneTimes()->delete(123);
```

**Version Differences:**
- **2021-01**: Creating onetimes requires `address_id` in the path: `POST /addresses/{address_id}/onetimes`
- **2021-11**: Creating onetimes uses the standard endpoint: `POST /onetimes` (address_id can be in the body)
- The SDK automatically handles these differences based on the current API version.

### Products

Products represent items available for subscription in your store.

**Note:** Products in API version 2021-01 are deprecated as of June 30, 2025. The recommended replacement is using Plans in 2021-11.

```php
// List products
foreach ($client->products()->list() as $product) {
    echo "Product: {$product->title}\n";
}

// With sorting (using enum - recommended)
use Recharge\Enums\Sort\ProductSort;

foreach ($client->products()->list(['sort_by' => ProductSort::TITLE_ASC]) as $product) {
    // Products sorted by title (A-Z)
}

// Filter by external product ID (2021-11)
foreach ($client->products()->list(['external_product_id' => 'prod_abc123']) as $product) {
    // Products with specific external ID
}

// Filter by Shopify product IDs
foreach ($client->products()->list(['shopify_product_ids' => '123,456']) as $product) {
    // Products from specific Shopify product IDs
}

// Get a product
// In 2021-11, use external_product_id (string)
// In 2021-01, use numeric id
$product = $client->products()->get('prod_abc123'); // or get(123) for 2021-01

// Create a product
$product = $client->products()->create([
    'title' => 'Coffee Subscription',
    'vendor' => 'Coffee Co',
    'description' => 'Premium coffee subscription',
    'requires_shipping' => true,
    'variants' => [
        [
            'external_variant_id' => 'var_123',
            'title' => '500g Bag',
            'price' => '29.99',
        ],
    ],
]);

// Update a product
$client->products()->update('prod_abc123', [
    'title' => 'Updated Coffee Subscription',
    'description' => 'Updated description',
]);

// Delete a product
$client->products()->delete('prod_abc123');

// Get count of products (requires API 2021-01, automatically handled)
$count = $client->products()->count();
```

**Version Differences:**
- **2021-11**: Uses `external_product_id` (string) as identifier for get/update/delete operations
- **2021-01**: Uses numeric `id` as identifier
- **2021-11**: Includes fields like `vendor`, `description`, `published_at`, `images`, `options`, `variants`
- **2021-01**: Includes fields like `shopify_product_id`, `subscription_defaults`, `discount_amount`, `discount_type`
- The SDK automatically handles identifier differences based on the current API version.

### Payment Methods

Payment methods represent customer payment information stored in Recharge.

**Note:** Payment methods are primarily available in API version 2021-11. Payment sources in 2021-01 are deprecated.

**Permissions Required:** Payment methods require specific API token scopes:
- `read_payment_methods` for read operations
- `write_payment_methods` for create/update/delete operations

```php
// List payment methods
foreach ($client->paymentMethods()->list() as $paymentMethod) {
    echo "Payment Method ID: {$paymentMethod->id}, Type: {$paymentMethod->paymentType?->value}\n";
}

// With sorting (using enum - recommended)
use Recharge\Enums\Sort\PaymentMethodSort;

foreach ($client->paymentMethods()->list(['sort_by' => PaymentMethodSort::CREATED_AT_DESC]) as $paymentMethod) {
    // Payment methods sorted by creation date (newest first)
}

// Filter by customer
foreach ($client->paymentMethods()->list(['customer_id' => 123]) as $paymentMethod) {
    // Payment methods for a specific customer
}

// Include billing addresses
foreach ($client->paymentMethods()->list(['include' => 'addresses']) as $paymentMethod) {
    // Payment methods with billing addresses included
}

// Get a payment method
$paymentMethod = $client->paymentMethods()->get(123);

// Check if payment method is valid
if ($paymentMethod->isValid()) {
    echo "Payment method is valid\n";
}

// Get last 4 digits (for credit cards)
if ($paymentMethod->isCreditCard()) {
    echo "Last 4: {$paymentMethod->getLast4()}\n";
}

// Create a payment method
use Recharge\Enums\PaymentType;
use Recharge\Enums\ProcessorName;

$paymentMethod = $client->paymentMethods()->create([
    'customer_id' => 123,
    'payment_type' => PaymentType::CREDIT_CARD->value,
    'processor_name' => ProcessorName::STRIPE->value,
    'processor_customer_token' => 'cus_abc123',
    'processor_payment_method_token' => 'pm_xyz789',
    'default' => true, // Set as default (will unset other defaults for this customer)
    'billing_address' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Main St',
        'city' => 'New York',
        'province' => 'NY',
        'zip' => '10001',
        'country_code' => 'US',
    ],
]);

// Update a payment method (limited - typically only default and billing_address)
$client->paymentMethods()->update(123, [
    'default' => true, // Set as default
    'billing_address' => [
        'address1' => '456 Oak Ave',
        'city' => 'Los Angeles',
    ],
]);

// Delete a payment method (only if not in use by active subscriptions)
$client->paymentMethods()->delete(123);
```

**Payment Types:**
- `PaymentType::CREDIT_CARD` - Credit card
- `PaymentType::PAYPAL` - PayPal
- `PaymentType::APPLE_PAY` - Apple Pay
- `PaymentType::GOOGLE_PAY` - Google Pay
- `PaymentType::SEPA_DEBIT` - SEPA Direct Debit

**Processor Names:**
- `ProcessorName::STRIPE` - Stripe
- `ProcessorName::BRAINTREE` - Braintree
- `ProcessorName::AUTHORIZE` - Authorize.net
- `ProcessorName::SHOPIFY_PAYMENTS` - Shopify Payments (read-only)
- `ProcessorName::MOLLIE` - Mollie

**Important Notes:**
- Only one payment method can be set as default per customer
- Many fields cannot be updated (e.g., card number, expiry) - create a new payment method instead
- For `shopify_payments` processor, updates are read-only (managed by Shopify)
- Payment methods can only be deleted if not in use by active subscriptions

### Shop (2021-01)

Shop endpoints provide basic store information and shipping countries.

**Note:** Shop endpoints are available in API version 2021-01. In 2021-11, this was unified/renamed as the `/store` endpoint.

```php
// Get shop information (automatically switches to 2021-01)
$shop = $client->shop()->get();

echo "Shop Name: {$shop->name}\n";
echo "Currency: {$shop->currency}\n";
echo "Timezone: {$shop->getTimezone()}\n";

// Get shipping countries
$shippingCountries = $client->shop()->getShippingCountries();

foreach ($shippingCountries as $country) {
    echo "Country: {$country['name']} ({$country['code']})\n";
}
```

**Version Differences:**
- **2021-01**: Uses `/shop` endpoint for shop information
- **2021-11**: Uses `/store` endpoint for store information (replaces shop)
- The SDK automatically handles version switching when using `shop()` method

**Note:** For 2021-11, use `$client->store()->get()` instead of `$client->shop()->get()`.

### Webhooks

Webhooks allow you to subscribe to Recharge events and receive notifications when those events occur.

```php
// List webhooks
foreach ($client->webhooks()->list() as $webhook) {
    echo "Webhook ID: {$webhook->id}, Address: {$webhook->address}\n";
    echo "Topics: " . implode(', ', $webhook->topics) . "\n";
}

// With sorting (using enum - recommended)
use Recharge\Enums\Sort\WebhookSort;

foreach ($client->webhooks()->list(['sort_by' => WebhookSort::CREATED_AT_DESC]) as $webhook) {
    // Webhooks sorted by creation date (newest first)
}

// Get a webhook
$webhook = $client->webhooks()->get(123);

// Create a webhook (using WebhookTopic enum - recommended)
use Recharge\Enums\WebhookTopic;

$webhook = $client->webhooks()->create([
    'address' => 'https://your-app.com/webhooks/recharge',
    'topics' => [
        WebhookTopic::CHARGE_CREATED->value,
        WebhookTopic::CHARGE_PROCESSED->value,
        WebhookTopic::SUBSCRIPTION_CREATED->value,
        WebhookTopic::SUBSCRIPTION_UPDATED->value,
    ],
]);

// String values also work (for backward compatibility)
$webhook = $client->webhooks()->create([
    'address' => 'https://your-app.com/webhooks/recharge',
    'topics' => [
        'charge/created',
        'charge/processed',
        'subscription/created',
        'subscription/updated',
    ],
]);

// Update a webhook
$webhook = $client->webhooks()->update(123, [
    'address' => 'https://your-app.com/webhooks/recharge-updated',
    'topics' => [
        WebhookTopic::CHARGE_CREATED->value,
        WebhookTopic::SUBSCRIPTION_CREATED->value,
    ],
]);

// Delete a webhook
$client->webhooks()->delete(123);
```

**Available Webhook Topics:**

Use the `WebhookTopic` enum for type-safe topic values:

```php
use Recharge\Enums\WebhookTopic;

// Charge events
WebhookTopic::CHARGE_CREATED
WebhookTopic::CHARGE_PROCESSED
WebhookTopic::CHARGE_FAILED
WebhookTopic::CHARGE_PAID
WebhookTopic::CHARGE_REFUNDED
// ... and more

// Subscription events
WebhookTopic::SUBSCRIPTION_CREATED
WebhookTopic::SUBSCRIPTION_UPDATED
WebhookTopic::SUBSCRIPTION_CANCELLED
WebhookTopic::SUBSCRIPTION_SKIPPED
WebhookTopic::SUBSCRIPTION_UNSKIPPED
// ... and more

// Customer, Order, Address, Bundle, Discount events
// See WebhookTopic enum for complete list
```

See all available topics: [Recharge Webhooks documentation](https://developer.rechargepayments.com/2021-11/webhooks#available-webhooks)

**Validating Incoming Webhooks:**

Use `WebhookValidator` to verify incoming webhook requests are authentic:

```php
use Recharge\Support\WebhookValidator;

// Get your API Client Secret from Recharge merchant portal:
// Tools and apps → API tokens → [Your Token] → API Client Secret
$clientSecret = 'your_api_client_secret';

// Get raw request body (must be exact JSON string)
$requestBody = file_get_contents('php://input'); // or from your HTTP framework

// Get signature from request headers
$signature = $_SERVER['HTTP_X_RECHARGE_HMAC_SHA256'] ?? ''; // or from your HTTP framework

// Validate signature
if (WebhookValidator::isValid($clientSecret, $requestBody, $signature)) {
    // Webhook is authentic - process it
    $data = json_decode($requestBody, true);
    // ... handle webhook data
} else {
    // Invalid signature - reject request
    http_response_code(401);
    exit('Invalid webhook signature');
}

// Alternative: Extract signature from headers array
$headers = getallheaders(); // or from your HTTP framework
if (WebhookValidator::validateFromHeaders($clientSecret, $requestBody, $headers)) {
    // Webhook is valid
}

// Alternative: Use with PSR-7 ServerRequestInterface
use Psr\Http\Message\ServerRequestInterface;

function handleWebhook(ServerRequestInterface $request): void
{
    $clientSecret = 'your_api_client_secret';
    
    if (WebhookValidator::validateFromPsr7($clientSecret, $request)) {
        $data = json_decode((string) $request->getBody(), true);
        // ... process webhook
    }
}
```

**Important Notes:**
- The API Client Secret is **different** from your API token
- Find it in: Recharge merchant portal → Tools and apps → API tokens → [Your Token]
- The request body must be the **exact JSON string** as received (even one space difference will fail)
- Always use `WebhookValidator` to prevent unauthorized requests

### Async Batches

Async batches allow you to perform bulk operations efficiently. Create a batch, add tasks to it, then process all tasks together.

```php
use Recharge\Enums\AsyncBatchType;
use Recharge\Enums\Sort\AsyncBatchSort;

// List async batches
foreach ($client->asyncBatches()->list() as $batch) {
    echo "Batch ID: {$batch->id}, Type: {$batch->batchType->value}, Status: {$batch->status->value}\n";
}

// With sorting (using enum - recommended)
foreach ($client->asyncBatches()->list(['sort_by' => AsyncBatchSort::CREATED_AT_DESC]) as $batch) {
    // Batches sorted by creation date (newest first)
}

// Get a batch
$batch = $client->asyncBatches()->get(123);

// Create a batch for discount creation
$batch = $client->asyncBatches()->create(AsyncBatchType::DISCOUNT_CREATE);

// Add tasks to the batch (up to 1,000 tasks per request)
$tasks = $client->asyncBatchTasks()->addTasks($batch->id, [
    ['body' => ['code' => 'DISCOUNT1', 'discount_type' => 'percentage', 'value' => 10]],
    ['body' => ['code' => 'DISCOUNT2', 'discount_type' => 'percentage', 'value' => 20]],
    ['body' => ['code' => 'DISCOUNT3', 'discount_type' => 'fixed_amount', 'value' => 500]],
]);

// Process the batch (submits all tasks for processing)
$batch = $client->asyncBatches()->process($batch->id);

// List tasks in a batch
foreach ($client->asyncBatchTasks()->list($batch->id) as $task) {
    if ($task->isSuccessful()) {
        echo "Task {$task->id} succeeded\n";
    } elseif ($task->hasFailed()) {
        echo "Task {$task->id} failed\n";
    }
}

// Filter tasks by IDs
$specificTasks = $client->asyncBatchTasks()->list($batch->id, [
    'ids' => [1, 2, 3], // Comma-separated or array
]);
```

**Available Batch Types:**

Use the `AsyncBatchType` enum for type-safe batch types. Some batch types are version-specific:

```php
use Recharge\Enums\AsyncBatchType;
use Recharge\Enums\ApiVersion;

// Discount operations (both versions)
AsyncBatchType::DISCOUNT_CREATE
AsyncBatchType::DISCOUNT_UPDATE
AsyncBatchType::DISCOUNT_DELETE

// Plans operations (2021-11 only)
AsyncBatchType::BULK_PLANS_CREATE
AsyncBatchType::BULK_PLANS_UPDATE
AsyncBatchType::BULK_PLANS_DELETE

// One-time operations (both versions)
AsyncBatchType::ONETIME_CREATE
AsyncBatchType::ONETIME_DELETE

// Check if a batch type is available in a version
$batchType = AsyncBatchType::BULK_PLANS_CREATE;
if ($batchType->isAvailableIn(ApiVersion::V2021_11)) {
    // Batch type is supported in 2021-11
}
```

**Batch Status:**

```php
use Recharge\Enums\AsyncBatchStatus;

$batch = $client->asyncBatches()->get(123);

if ($batch->status === AsyncBatchStatus::NOT_STARTED) {
    // Batch hasn't been processed yet
} elseif ($batch->status === AsyncBatchStatus::PROCESSING) {
    // Batch is currently processing
} elseif ($batch->status === AsyncBatchStatus::COMPLETED) {
    // Batch completed successfully
} elseif ($batch->status === AsyncBatchStatus::FAILED) {
    // Batch failed
}

// Helper methods
if ($batch->isTerminal()) {
    // Batch is in a terminal state (completed or failed)
}
```

**Important Notes:**
- Maximum 1,000 tasks per request when adding tasks
- Maximum 10,000 tasks per batch total
- Batch types are version-specific - the SDK validates that batch types are valid for the current API version
- Tasks contain a `body` object with the payload for the operation (same as individual API calls)
- Use `process()` after adding all tasks to submit the batch for processing
- Monitor batch status using `get()` or via webhooks (`async_batch/processed` topic)

## API Version

```php
use Recharge\Enums\ApiVersion;

// Default is 2021-11
$client = new RechargeClient($token);

// Use 2021-01
$client = new RechargeClient($token, ApiVersion::V2021_01);

// Switch versions
$client->setApiVersion(ApiVersion::V2021_11);
```

## Available Resources

- `subscriptions()` - Manage subscriptions
- `customers()` - Manage customers
- `addresses()` - Manage addresses
- `discounts()` - Manage discounts
- `bundles()` - Manage bundles
- `charges()` - Manage charges (with full CRUD and action methods)
- `checkouts()` - Manage checkouts (BigCommerce/Custom only, requires Pro plan)
- `collections()` - Manage collections
- `credits()` - Manage credits (2021-11 only)
- `metafields()` - Manage metafields
- `oneTimes()` - Manage one-time purchases
- `orders()` - Manage orders
- `paymentMethods()` - Manage payment methods (2021-11, requires specific scopes)
- `plans()` - Manage plans (2021-11 only, replaces deprecated products plan operations)
- `products()` - Manage products (with sorting support)
- `shop()` - Get shop info (2021-01 only, use store() for 2021-11)
- `store()` - Get store info (2021-11, replaces shop endpoint from 2021-01)

## Error Handling

```php
use Recharge\Exceptions\{RechargeApiException, ValidationException};

try {
    $sub = $client->subscriptions()->get(123);
} catch (ValidationException $e) {
    // Client-side validation errors
    print_r($e->getErrors());
} catch (RechargeApiException $e) {
    // API errors
    echo $e->getMessage();
}
```

## Pagination

Pagination is automatic - just iterate:

```php
// Fetches all pages automatically
foreach ($client->subscriptions()->list() as $sub) {
    echo $sub->id . "\n";
}

// With page size
foreach ($client->subscriptions()->list(['limit' => 100]) as $sub) {
    // Process in batches of 100
}

// Get first item
$first = $client->subscriptions()->list()->first();

// Get first N items
$items = $client->subscriptions()->list()->take(50);
```

## Development

```bash
# Run tests
composer test

# Check code style
composer cs:check

# Fix code style
composer cs:fix

# Run static analysis
composer analyse

# Run all quality checks
composer quality
```

### Git Hooks

Pre-commit hooks are automatically installed to ensure code quality:

```bash
# Hooks auto-install with composer install/update

# Manually manage
composer hooks:install
composer hooks:uninstall

# Bypass if needed
git commit --no-verify
```

## Documentation

- [Recharge API 2021-11](https://developer.rechargepayments.com/2021-11)
- [Recharge API 2021-01](https://developer.rechargepayments.com/2021-01)
- [Changelog](CHANGELOG.md)

## License

MIT License - See [LICENSE](LICENSE)
