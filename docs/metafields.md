# Metafields

Manage metafield resources in Recharge.

## List Metafields

```php
// List metafields
foreach ($client->metafields()->list() as $metafield) {
    echo "Metafield: {$metafield->namespace}.{$metafield->key} = {$metafield->value}\n";
}

// With sorting
use Recharge\Enums\Sort\MetafieldSort;

foreach ($client->metafields()->list(['sort_by' => MetafieldSort::UPDATED_AT_DESC]) as $metafield) {
    // Metafields sorted by update date (newest first)
}

// Filter by owner resource
foreach ($client->metafields()->list(['owner_resource' => 'customer']) as $metafield) {
    // Metafields for customers only
}

// Filter by owner ID and namespace
foreach ($client->metafields()->list([
    'owner_resource' => 'subscription',
    'owner_id' => 123,
    'namespace' => 'custom',
]) as $metafield) {
    // Metafields for a specific subscription with custom namespace
}
```

## Get Single Metafield

```php
$metafield = $client->metafields()->get(123);
```

## Create Metafield

```php
// Create a metafield
$metafield = $client->metafields()->create([
    'owner_resource' => 'customer',
    'owner_id' => 123,
    'namespace' => 'custom',
    'key' => 'preferred_language',
    'value' => 'en',
    'type' => 'single_line_text_field',
    'description' => 'Customer preferred language',
]);

// Create a metafield with JSON value
$metafield = $client->metafields()->create([
    'owner_resource' => 'subscription',
    'owner_id' => 456,
    'namespace' => 'custom',
    'key' => 'metadata',
    'value' => json_encode(['source' => 'api', 'version' => '1.0']),
    'type' => 'json',
]);
```

## Update Metafield

```php
$client->metafields()->update(123, [
    'value' => 'es',
    'description' => 'Updated to Spanish',
]);
```

## Delete Metafield

```php
$client->metafields()->delete(123);
```

## Get Count

```php
// Get count of metafields (requires API 2021-01, automatically handled)
$count = $client->metafields()->count([
    'owner_resource' => 'customer',
    'namespace' => 'custom',
]);
```

**Note:** Count endpoint is only available in API version 2021-01.

See [Sorting Documentation](sorting.md) for available sort options.
