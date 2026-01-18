# Subscriptions

Manage subscription resources in Recharge.

## List Subscriptions

```php
// Basic listing
foreach ($client->subscriptions()->list() as $sub) {
    echo $sub->id . " - " . $sub->getProductTitle() . "\n";
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

## Get Single Subscription

```php
$subscription = $client->subscriptions()->get(123);
```

## Get Count

```php
// Get count of subscriptions (requires API 2021-01, automatically handled)
$count = $client->subscriptions()->count(['status' => 'ACTIVE']);

// Note: Count endpoints are only available in API version 2021-01.
// The count() method automatically switches to 2021-01 for the request.
```

## Create Subscription

```php
$subscription = $client->subscriptions()->create([
    'customer_id' => 456,
    'quantity' => 2,
    'price' => 29.99,
    'order_interval_unit' => 'month',
    'order_interval_frequency' => 1,
]);
```

## Update & Delete

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

## Available Sort Options

**Subscriptions (`SubscriptionSort`):**
- `SubscriptionSort::ID_ASC`, `SubscriptionSort::ID_DESC` (default)
- `SubscriptionSort::CREATED_AT_ASC`, `SubscriptionSort::CREATED_AT_DESC`
- `SubscriptionSort::UPDATED_AT_ASC`, `SubscriptionSort::UPDATED_AT_DESC`

## Bulk Operations

Bulk operations allow you to create, update, or delete multiple subscriptions for an address in a single request. These are only available in API version 2021-01 (automatically handled).

### Bulk Create Subscriptions

```php
// Create multiple subscriptions for an address (requires API 2021-01, automatically handled)
$result = $client->subscriptions()->bulkCreate(123, [
    [
        'quantity' => 1,
        'price' => 29.99,
        'order_interval_unit' => 'month',
        'order_interval_frequency' => 1,
    ],
    [
        'quantity' => 2,
        'price' => 49.99,
        'order_interval_unit' => 'month',
        'order_interval_frequency' => 2,
    ],
]);
```

### Bulk Update Subscriptions

```php
// Update multiple subscriptions for an address (requires API 2021-01, automatically handled)
$result = $client->subscriptions()->bulkUpdate(123, [
    [
        'id' => 456,
        'quantity' => 3,
        'price' => 39.99,
    ],
    [
        'id' => 457,
        'quantity' => 1,
        'price' => 19.99,
    ],
]);
```

### Bulk Delete Subscriptions

```php
// Delete multiple subscriptions for an address (requires API 2021-01, automatically handled)
$client->subscriptions()->bulkDelete(123, [456, 457, 458]);
```

**Note:** Bulk operations are only available in API version 2021-01. The methods automatically switch to 2021-01 for the request.

See [Sorting Documentation](../README.md#sorting) for more details.
