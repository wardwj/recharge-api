# Credits

Manage store credit resources in Recharge (2021-11 only).

## List Credits

```php
// List credits
foreach ($client->credits()->list() as $credit) {
    echo "Credit ID: {$credit->id}, Amount: {$credit->amount}\n";
}

// Filter by customer
foreach ($client->credits()->list(['customer_id' => 123]) as $credit) {
    // Credits for a specific customer
}
```

## Get Single Credit

```php
$credit = $client->credits()->get(123);
```

## Create Credit

```php
$credit = $client->credits()->create([
    'customer_id' => 123,
    'amount' => 25.00,
    'currency' => 'USD',
    'note' => 'Promotional credit',
]);
```

## Update Credit

```php
$client->credits()->update(123, [
    'amount' => 50.00,
    'note' => 'Updated promotional credit',
]);
```

## Delete Credit

```php
$client->credits()->delete(123);
```

**Note:** Credits are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.
