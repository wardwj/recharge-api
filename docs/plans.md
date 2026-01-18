# Plans

Manage subscription plan resources in Recharge (2021-11 only).

**Note:** Plans are only available in API version 2021-11. They replace the deprecated products endpoint for plan-related operations in 2021-01.

## List Plans

```php
// List plans
foreach ($client->plans()->list() as $plan) {
    echo "Plan ID: {$plan->id}, Title: {$plan->title}\n";
}

// With sorting
use Recharge\Enums\Sort\PlanSort;

foreach ($client->plans()->list(['sort_by' => PlanSort::CREATED_AT_DESC]) as $plan) {
    // Plans sorted by creation date (newest first)
}

// Filter by type
use Recharge\Enums\PlanType;

foreach ($client->plans()->list(['type' => PlanType::SUBSCRIPTION->value]) as $plan) {
    // Only subscription plans
}
```

## Get Single Plan

```php
$plan = $client->plans()->get(123);
```

## Create Plan

```php
$plan = $client->plans()->create([
    'external_product_id' => ['ecommerce' => 'shopify', 'product_id' => '123'],
    'type' => 'subscription',
    'title' => 'Monthly Coffee Plan',
    'subscription_preferences' => [
        'order_interval_unit' => 'month',
        'order_interval_frequency' => 1,
    ],
]);
```

## Update Plan

```php
$client->plans()->update(123, [
    'title' => 'Updated Plan Title',
    'sort_order' => 5,
]);
```

## Delete Plan

```php
$client->plans()->delete(123);
```

## Bulk Operations

```php
// Create multiple plans (up to 20 per request)
$plans = $client->plans()->createBulk([
    ['external_product_id' => ['ecommerce' => 'shopify', 'product_id' => '123'], 'type' => 'subscription', 'title' => 'Plan 1'],
    ['external_product_id' => ['ecommerce' => 'shopify', 'product_id' => '456'], 'type' => 'subscription', 'title' => 'Plan 2'],
]);

// Update multiple plans (up to 20 per request)
$client->plans()->updateBulk([
    ['id' => 1, 'title' => 'Updated Plan 1'],
    ['id' => 2, 'title' => 'Updated Plan 2'],
]);

// Delete multiple plans (pass plan IDs as query parameters)
$client->plans()->deleteBulk([1, 2, 3]);
```

**Plan Types:**
- `PlanType::SUBSCRIPTION` - Subscription plan
- `PlanType::PREPAID` - Prepaid plan
- `PlanType::ONETIME` - One-time plan

**Note:** Plans are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.

See [Sorting Documentation](sorting.md) for available sort options.
