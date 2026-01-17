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
```

### Get Single Resource

```php
$subscription = $client->subscriptions()->get(123);
$customer = $client->customers()->get(456);
$order = $client->orders()->get(789);
```

### Create Subscription

```php
use Recharge\Requests\CreateSubscriptionData;

$subscription = $client->subscriptions()->create(
    new CreateSubscriptionData(
        customerId: 456,
        quantity: 2,
        price: 29.99,
        interval: '1 month'
    )
);
```

### Update & Delete

```php
use Recharge\Requests\UpdateSubscriptionData;

// Update
$client->subscriptions()->update(
    123,
    new UpdateSubscriptionData(quantity: 3, price: 39.99)
);

// Cancel
$client->subscriptions()->cancel(123, 'Customer requested');

// Delete
$client->subscriptions()->delete(123);
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
- `charges()` - Manage charges
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
