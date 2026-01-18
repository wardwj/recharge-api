# Discounts

Manage discount codes in Recharge.

## List Discounts

```php
// List discounts
foreach ($client->discounts()->list() as $discount) {
    echo $discount->code . " - " . $discount->value . "\n";
}

// With sorting
use Recharge\Enums\Sort\DiscountSort;

foreach ($client->discounts()->list(['sort_by' => DiscountSort::CREATED_AT_DESC]) as $discount) {
    // Discounts sorted by creation date (newest first)
}
```

## Get Single Discount

```php
$discount = $client->discounts()->get(123);
```

## Create Discount

```php
$discount = $client->discounts()->create([
    'code' => 'SAVE10',
    'discount_type' => 'percentage', // or 'value_type' in 2021-11
    'value' => 10,
    'duration' => 'forever',
    'status' => 'enabled',
]);
```

## Update & Delete

```php
// Update a discount
$client->discounts()->update(123, ['value' => 15]);

// Delete a discount
$client->discounts()->delete(123);
```

## Get Count

```php
// Get count of discounts (requires API 2021-01, automatically handled)
$count = $client->discounts()->count(['status' => 'enabled']);
```

## Apply & Remove

```php
// Apply discount to an address
$client->discounts()->applyToAddress(456, ['discount_code' => 'SAVE10']);

// Apply discount to a charge
$client->discounts()->applyToCharge(789, ['discount_code' => 'SAVE10']);

// Remove a discount
$client->discounts()->remove(['address_id' => 456]);
```

See [Sorting Documentation](sorting.md) for available sort options.
