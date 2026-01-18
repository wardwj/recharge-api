# One-Times

Manage one-time purchase resources in Recharge.

One-times are non-recurring line items attached to a QUEUED charge. They represent one-off purchases rather than subscriptions.

## List One-Times

```php
// List one-times
foreach ($client->oneTimes()->list() as $onetime) {
    echo "One-Time ID: {$onetime->id}, Price: {$onetime->price}\n";
}

// With sorting
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
```

## Get Single One-Time

```php
$onetime = $client->oneTimes()->get(123);
```

## Create One-Time

```php
// Note: In API version 2021-01, address_id must be in the path.
// In 2021-11, address_id can be in the request body.
// The SDK automatically handles version differences.
$onetime = $client->oneTimes()->create([
    'address_id' => 123, // Required
    'external_variant_id' => 'variant_456',
    'quantity' => 2,
    'price' => 29.99,
]);
```

## Update One-Time

```php
$client->oneTimes()->update(123, [
    'quantity' => 3,
    'price' => 39.99,
]);
```

## Delete One-Time

```php
$client->oneTimes()->delete(123);
```

**Version Differences:**
- **2021-01**: Creating onetimes requires `address_id` in the path: `POST /addresses/{address_id}/onetimes`
- **2021-11**: Creating onetimes uses the standard endpoint: `POST /onetimes` (address_id can be in the body)
- The SDK automatically handles these differences based on the current API version.

See [Sorting Documentation](sorting.md) for available sort options.
