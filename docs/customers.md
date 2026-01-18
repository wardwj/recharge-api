# Customers

Manage customer resources in Recharge.

## List Customers

```php
// List customers
foreach ($client->customers()->list() as $customer) {
    echo $customer->email . "\n";
}

// With sorting
use Recharge\Enums\Sort\CustomerSort;

foreach ($client->customers()->list(['sort_by' => CustomerSort::CREATED_AT_DESC]) as $customer) {
    // Customers sorted by creation date (newest first)
}
```

## Get Single Customer

```php
$customer = $client->customers()->get(123);
```

## Get Count

```php
// Get count of customers (requires API 2021-01, automatically handled)
$count = $client->customers()->count(['email' => 'customer@example.com']);
```

## Create Customer

```php
$customer = $client->customers()->create([
    'email' => 'customer@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);
```

## Update & Delete

```php
// Update
$client->customers()->update(123, [
    'first_name' => 'Jane',
    'last_name' => 'Smith',
]);

// Delete
$client->customers()->delete(123);
```

## Send Notification

```php
// Using enum (recommended)
use Recharge\Enums\NotificationTemplate;

$client->customers()->sendNotification(
    123,
    NotificationTemplate::GET_ACCOUNT_ACCESS
);

// Using string template name
$client->customers()->sendNotification(
    123,
    'upcoming_charge'
);

// With template variables (if required by template)
$client->customers()->sendNotification(
    123,
    NotificationTemplate::UPCOMING_CHARGE,
    [
        'charge_date' => '2024-12-31',
        'amount' => '29.99',
    ]
);
```

**Supported Templates:**
- `NotificationTemplate::GET_ACCOUNT_ACCESS` - Send account access link/code
- `NotificationTemplate::UPCOMING_CHARGE` - Send notification about upcoming charge

Note: Both templates are supported in API versions 2021-01 and 2021-11.

See [Sorting Documentation](sorting.md) for available sort options.
