# Products

Manage product resources in Recharge.

**Note:** Products in API version 2021-01 are deprecated as of June 30, 2025. The recommended replacement is using Plans in 2021-11.

## List Products

```php
// List products
foreach ($client->products()->list() as $product) {
    echo "Product: {$product->title}\n";
}

// With sorting
use Recharge\Enums\Sort\ProductSort;

foreach ($client->products()->list(['sort_by' => ProductSort::TITLE_ASC]) as $product) {
    // Products sorted by title (A-Z)
}

// Filter by external product ID (2021-11)
foreach ($client->products()->list(['external_product_id' => 'prod_abc123']) as $product) {
    // Products with specific external ID
}

// Filter by Shopify product IDs
foreach ($client->products()->list(['shopify_product_ids' => '123,456']) as $product) {
    // Products from specific Shopify product IDs
}
```

## Get Single Product

```php
// In 2021-11, use external_product_id (string)
// In 2021-01, use numeric id
$product = $client->products()->get('prod_abc123'); // or get(123) for 2021-01
```

## Create Product

```php
$product = $client->products()->create([
    'title' => 'Coffee Subscription',
    'vendor' => 'Coffee Co',
    'description' => 'Premium coffee subscription',
    'requires_shipping' => true,
    'variants' => [
        [
            'external_variant_id' => 'var_123',
            'title' => '500g Bag',
            'price' => '29.99',
        ],
    ],
]);
```

## Update Product

```php
$client->products()->update('prod_abc123', [
    'title' => 'Updated Coffee Subscription',
    'description' => 'Updated description',
]);
```

## Delete Product

```php
$client->products()->delete('prod_abc123');
```

## Get Count

```php
// Get count of products (requires API 2021-01, automatically handled)
$count = $client->products()->count();
```

**Version Differences:**
- **2021-11**: Uses `external_product_id` (string) as identifier for get/update/delete operations
- **2021-01**: Uses numeric `id` as identifier
- **2021-11**: Includes fields like `vendor`, `description`, `published_at`, `images`, `options`, `variants`
- **2021-01**: Includes fields like `shopify_product_id`, `subscription_defaults`, `discount_amount`, `discount_type`
- The SDK automatically handles identifier differences based on the current API version.

See [Sorting Documentation](sorting.md) for available sort options.
