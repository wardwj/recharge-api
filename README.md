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

## Basic Usage

### List Resources

```php
// List subscriptions
foreach ($client->subscriptions()->list() as $sub) {
    echo $sub->id . "\n";
}

// With filters
foreach ($client->subscriptions()->list(['status' => 'ACTIVE']) as $sub) {
    // Process active subscriptions
}

// With sorting
use Recharge\Enums\Sort\SubscriptionSort;

foreach ($client->subscriptions()->list(['sort_by' => SubscriptionSort::CREATED_AT_DESC]) as $sub) {
    // Subscriptions sorted by creation date (newest first)
}
```

### Get, Create, Update, Delete

```php
// Get single resource
$subscription = $client->subscriptions()->get(123);

// Create resource
$subscription = $client->subscriptions()->create([
    'customer_id' => 456,
    'quantity' => 2,
    'price' => 29.99,
]);

// Update resource
$client->subscriptions()->update(123, ['quantity' => 3]);

// Delete resource
$client->subscriptions()->delete(123);
```

### Pagination

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

### Sorting

The SDK supports sorting using type-safe enums. See [Sorting Documentation](docs/sorting.md) for all available sort options.

```php
use Recharge\Enums\Sort\SubscriptionSort;

// Using enums (recommended)
foreach ($client->subscriptions()->list(['sort_by' => SubscriptionSort::CREATED_AT_DESC]) as $sub) {
    // ...
}

// String values also work
foreach ($client->subscriptions()->list(['sort_by' => 'created_at-desc']) as $sub) {
    // ...
}
```

### API Version

```php
use Recharge\Enums\ApiVersion;

// Default is 2021-11
$client = new RechargeClient($token);

// Use 2021-01
$client = new RechargeClient($token, ApiVersion::V2021_01);

// Switch versions
$client->setApiVersion(ApiVersion::V2021_11);
```

The SDK automatically handles version differences for resources and endpoints.

## Available Resources

Detailed examples for each resource are available in the [documentation](docs/):

- [Subscriptions](docs/subscriptions.md) - Manage subscriptions
- [Charges](docs/charges.md) - Manage charges with full CRUD and action methods
- [Customers](docs/customers.md) - Manage customers and send notifications
- [Orders](docs/orders.md) - Manage orders
- [Discounts](docs/discounts.md) - Manage discount codes
- [Addresses](docs/addresses.md) - Manage customer addresses
- [Payment Methods](docs/payment-methods.md) - Manage customer payment methods (2021-11)
- [Plans](docs/plans.md) - Manage subscription plans (2021-11)
- [Products](docs/products.md) - Manage products
- [Bundles](docs/bundles.md) - Manage bundle selections (2021-11)
- [Checkouts](docs/checkouts.md) - Manage checkouts (BigCommerce/Custom)
- [Collections](docs/collections.md) - Manage product collections (2021-11)
- [Credits](docs/credits.md) - Manage store credits (2021-11)
- [Metafields](docs/metafields.md) - Manage metafields
- [One-Times](docs/one-times.md) - Manage one-time purchases
- [Webhooks](docs/webhooks.md) - Manage webhooks and validate incoming requests
- [Async Batches](docs/async-batches.md) - Perform bulk operations
- [Shop/Store](docs/shop.md) - Get shop/store information

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

## Documentation

- [Resource Documentation](docs/) - Detailed examples for each resource
- [Recharge API 2021-11](https://developer.rechargepayments.com/2021-11)
- [Recharge API 2021-01](https://developer.rechargepayments.com/2021-01)
- [Changelog](CHANGELOG.md)

## License

MIT License - See [LICENSE](LICENSE)
