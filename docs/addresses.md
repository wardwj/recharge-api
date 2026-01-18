# Addresses

Manage customer address resources in Recharge.

## List Addresses

```php
// List addresses
foreach ($client->addresses()->list() as $address) {
    echo "Address ID: {$address->id}, City: {$address->city}\n";
}

// Filter by customer
foreach ($client->addresses()->list(['customer_id' => 123]) as $address) {
    // Addresses for a specific customer
}
```

## Get Single Address

```php
$address = $client->addresses()->get(123);
```

## Get Count

```php
// Get count of addresses (requires API 2021-01, automatically handled)
$count = $client->addresses()->count(['customer_id' => 123]);
```

## Validate Address

```php
// Validate an address before creating (requires API 2021-01, automatically handled)
$validation = $client->addresses()->validate([
    'address1' => '123 Main St',
    'city' => 'New York',
    'province' => 'NY',
    'zip' => '10001',
    'country' => 'United States',
    'country_code' => 'US',
]);

// Check validation results
if (isset($validation['valid']) && $validation['valid']) {
    // Address is valid, proceed with creation
}
```

**Note:** Address validation is only available in API version 2021-01.

## Create Address

```php
$address = $client->addresses()->create([
    'customer_id' => 123,
    'address1' => '123 Main St',
    'city' => 'New York',
    'province' => 'NY',
    'zip' => '10001',
    'country' => 'United States',
    'country_code' => 'US',
]);
```

## Update Address

```php
$client->addresses()->update(123, [
    'address1' => '456 Oak Ave',
    'city' => 'Los Angeles',
]);
```

## Delete Address

```php
$client->addresses()->delete(123);
```

**Note:** All subscriptions associated with an address must be moved to a different address or cancelled before deletion.
