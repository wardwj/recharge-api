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
- `OrderSort` - For orders (2021-01 only)
- `CustomerSort` - For customers
- `DiscountSort` - For discounts
- `BundleSort` - For bundles
- `MetafieldSort` - For metafields (2021-01 only)

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
- Note: Metafields sorting is only available in API version 2021-01. The SDK automatically switches to 2021-01 when sorting is used.

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
- `orders()` - Manage orders
- `products()` - List products
- `store()` - Get store info

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
