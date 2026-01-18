# Orders

Manage order resources in Recharge.

## List Orders

```php
// List orders
foreach ($client->orders()->list() as $order) {
    echo "Order ID: {$order->id}, Status: {$order->status?->value}\n";
}

// With sorting
use Recharge\Enums\Sort\OrderSort;

foreach ($client->orders()->list(['sort_by' => OrderSort::CREATED_AT_DESC]) as $order) {
    // Orders sorted by creation date (newest first)
}
```

## Get Single Order

```php
$order = $client->orders()->get(123);
```

## Get Count

```php
// Get count of orders (requires API 2021-01, automatically handled)
$count = $client->orders()->count(['status' => 'SUCCESS']);
```

## Update & Delete

```php
// Update
$client->orders()->update(123, [
    'scheduled_at' => '2024-12-31',
]);

// Delete
$client->orders()->delete(123);
```

## Clone Order

```php
// Clone an order
$clonedOrder = $client->orders()->clone(123, [
    'scheduled_at' => '2025-01-15',
]);
```

## Delay Order

```php
// Delay an order
$delayedOrder = $client->orders()->delay(123, [
    'scheduled_at' => '2025-02-01',
]);
```

See [Sorting Documentation](sorting.md) for available sort options.
