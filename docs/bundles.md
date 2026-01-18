# Bundle Selections

Manage bundle selection resources in Recharge (2021-11 only).

## List Bundle Selections

```php
// List bundle selections
foreach ($client->bundles()->list() as $bundle) {
    echo "Bundle Selection ID: {$bundle->id}\n";
}

// With sorting
use Recharge\Enums\Sort\BundleSort;

foreach ($client->bundles()->list(['sort_by' => BundleSort::UPDATED_AT_DESC]) as $bundle) {
    // Bundle selections sorted by update date (newest first)
}
```

## Get Single Bundle Selection

```php
$bundle = $client->bundles()->get(123);
```

## Create Bundle Selection

```php
$bundle = $client->bundles()->create([
    'bundle_variant_id' => 456,
    'purchase_item_id' => 789,
]);
```

## Update & Delete

```php
// Update a bundle selection
$client->bundles()->update(123, ['purchase_item_id' => 999]);

// Delete a bundle selection
$client->bundles()->delete(123);
```

**Note:** Bundle selections are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.

See [Sorting Documentation](sorting.md) for available sort options.
