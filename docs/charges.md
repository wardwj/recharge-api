# Charges

Manage charge resources in Recharge.

## List Charges

```php
// List charges
foreach ($client->charges()->list(['status' => 'queued']) as $charge) {
    echo "Charge ID: {$charge->id}, Status: {$charge->status?->value}\n";
}

// With sorting
use Recharge\Enums\Sort\ChargeSort;

foreach ($client->charges()->list(['sort_by' => ChargeSort::SCHEDULED_AT_ASC]) as $charge) {
    // Charges sorted by scheduled date (earliest first)
}
```

## Get Single Charge

```php
$charge = $client->charges()->get(123);
```

## Get Count

```php
// Get count of charges (requires API 2021-01, automatically handled)
$queuedCount = $client->charges()->count(['status' => 'queued']);
```

## Actions

```php
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

See [Sorting Documentation](sorting.md) for available sort options.
