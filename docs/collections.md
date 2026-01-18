# Collections

Manage product collection resources in Recharge (2021-11 only).

## List Collections

```php
// List collections
foreach ($client->collections()->list() as $collection) {
    echo "Collection: {$collection->title}\n";
}

// Filter by title
foreach ($client->collections()->list(['title' => 'Featured']) as $collection) {
    // Collections with title containing 'Featured'
}
```

## Get Single Collection

```php
$collection = $client->collections()->get(123);
```

## Create Collection

```php
$collection = $client->collections()->create([
    'title' => 'Featured Products',
    'description' => 'Our featured product collection',
    'sort_order' => 'title-asc',
]);
```

## Update Collection

```php
$client->collections()->update(123, [
    'title' => 'Updated Collection',
    'sort_order' => 'created_at-desc',
]);
```

## List Products in Collection

```php
foreach ($client->collections()->listProducts(123) as $product) {
    echo "Product: {$product->title}\n";
}
```

## Bulk Delete Products

```php
// Bulk delete products from a collection (limit 250 per request)
$client->collections()->deleteProductsBulk(123, [456, 789, 101]);
```

**Note:** Collections are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.
