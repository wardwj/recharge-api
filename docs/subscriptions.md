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

See [Sorting Documentation](../README.md#sorting) for more details.
