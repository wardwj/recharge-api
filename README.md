# Recharge API PHP Client

PHP SDK for the Recharge Payments API supporting both API versions 2021-01 and 2021-11.

## Features

- ✅ Support for both API versions (2021-01 and 2021-11)
- ✅ Version-specific DTOs for type safety
- ✅ Fluent interface for easy API interaction
- ✅ Automatic DTO version selection based on API version
- ✅ Comprehensive exception handling

## Installation

```bash
composer require vendor/recharge-api
```

Or add to your `composer.json`:

```json
{
    "require": {
        "vendor/recharge-api": "^1.0"
    }
}
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Recharge\Client;

// Initialize client with API token (defaults to 2021-11)
$client = new Client('your_api_token');

// Get store information
$store = $client->store()->get();
echo "Store: " . $store->getName() . "\n";

// List customers
$customers = $client->customers()->list(['limit' => 10]);
foreach ($customers as $customer) {
    echo "Customer: " . $customer->getEmail() . "\n";
}

// List subscriptions
$subscriptions = $client->subscriptions()->list(['limit' => 10]);
foreach ($subscriptions as $subscription) {
    echo "Subscription: " . $subscription->getId() . "\n";
}
```

## API Version Support

The client supports both API versions 2021-01 and 2021-11. Version-specific DTOs are automatically selected based on the client's API version setting.

### Using API Version 2021-11 (default)

```php
$client = new Client('your_api_token', Client::API_VERSION_2021_11);
// or simply
$client = new Client('your_api_token');

$store = $client->store()->get();
// Returns: Recharge\DTO\V2021_11\Store
```

### Using API Version 2021-01

```php
$client = new Client('your_api_token', Client::API_VERSION_2021_01);

$store = $client->store()->get();
// Returns: Recharge\DTO\V2021_01\Store
```

### Switching API Versions

```php
$client = new Client('your_api_token');

// Switch to 2021-01
$client->setApiVersion(Client::API_VERSION_2021_01);
$store = $client->store()->get(); // Uses 2021-01 DTO

// Switch back to 2021-11
$client->setApiVersion(Client::API_VERSION_2021_11);
$store = $client->store()->get(); // Uses 2021-11 DTO
```

## Available Resources

- **Customers** - `$client->customers()`
- **Subscriptions** - `$client->subscriptions()`
- **Charges** - `$client->charges()`
- **Orders** - `$client->orders()`
- **Addresses** - `$client->addresses()`
- **Products** - `$client->products()`
- **Store** - `$client->store()`

## Examples

### Customers

```php
// List customers
$customers = $client->customers()->list(['limit' => 10]);

// Get a customer
$customer = $client->customers()->get(123);

// Create a customer
$customer = $client->customers()->create([
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe'
]);

// Update a customer
$customer = $client->customers()->update(123, [
    'first_name' => 'Jane'
]);

// Delete a customer
$client->customers()->delete(123);
```

### Subscriptions

```php
// List subscriptions
$subscriptions = $client->subscriptions()->list(['status' => 'ACTIVE']);

// Get a subscription
$subscription = $client->subscriptions()->get(456);

// Create a subscription
$subscription = $client->subscriptions()->create([
    'customer_id' => 123,
    'address_id' => 789,
    'quantity' => 1,
    'price' => '29.99'
]);

// Cancel a subscription
$subscription = $client->subscriptions()->cancel(456, [
    'cancellation_reason' => 'customer_request'
]);

// Activate a subscription
$subscription = $client->subscriptions()->activate(456);
```

## Testing

### Quick Test Script

Run the example script with your API token:

```bash
php example.php YOUR_API_TOKEN
```

Or set the token as an environment variable:

```bash
export RECHARGE_API_TOKEN=your_api_token
php example.php
```

### PHPUnit Tests

Run the test suite:

```bash
export RECHARGE_API_TOKEN=your_api_token
vendor/bin/phpunit
```

## Exception Handling

The client throws specific exceptions for different error scenarios:

```php
use Recharge\Exceptions\RechargeException;
use Recharge\Exceptions\RechargeAuthenticationException;
use Recharge\Exceptions\RechargeApiException;

try {
    $customer = $client->customers()->get(123);
} catch (RechargeAuthenticationException $e) {
    // Authentication failed (401, 403)
    echo "Auth error: " . $e->getMessage();
} catch (RechargeApiException $e) {
    // API error (4xx, 5xx)
    echo "API error: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
} catch (RechargeException $e) {
    // General error
    echo "Error: " . $e->getMessage();
}
```

## Requirements

- PHP 7.4 or higher
- Composer
- Guzzle HTTP 7.0 or higher

## License

MIT

## Documentation

- [Recharge API Documentation (2021-11)](https://developer.rechargepayments.com/2021-11/)
- [Recharge API Documentation (2021-01)](https://developer.rechargepayments.com/2021-01/)
