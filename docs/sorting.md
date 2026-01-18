# Sorting

The SDK supports sorting for list operations using type-safe enums or strings. Using enums is recommended for better IDE support and type safety.

## Available Sort Enums

- `SubscriptionSort` - For subscriptions
- `ChargeSort` - For charges
- `OrderSort` - For orders
- `CustomerSort` - For customers
- `DiscountSort` - For discounts
- `BundleSort` - For bundles
- `MetafieldSort` - For metafields
- `OneTimeSort` - For one-times
- `ProductSort` - For products
- `PaymentMethodSort` - For payment methods
- `PlanSort` - For plans (2021-11 only)
- `WebhookSort` - For webhooks

## Sort Options by Resource

### Subscriptions (`SubscriptionSort`)
- `SubscriptionSort::ID_ASC`, `SubscriptionSort::ID_DESC` (default)
- `SubscriptionSort::CREATED_AT_ASC`, `SubscriptionSort::CREATED_AT_DESC`
- `SubscriptionSort::UPDATED_AT_ASC`, `SubscriptionSort::UPDATED_AT_DESC`

### Charges (`ChargeSort`)
- `ChargeSort::ID_ASC`, `ChargeSort::ID_DESC` (default)
- `ChargeSort::CREATED_AT_ASC`, `ChargeSort::CREATED_AT_DESC`
- `ChargeSort::UPDATED_AT_ASC`, `ChargeSort::UPDATED_AT_DESC`
- `ChargeSort::SCHEDULED_AT_ASC`, `ChargeSort::SCHEDULED_AT_DESC`

### Orders (`OrderSort`)
- `OrderSort::ID_ASC`, `OrderSort::ID_DESC` (default)
- `OrderSort::CREATED_AT_ASC`, `OrderSort::CREATED_AT_DESC`
- `OrderSort::UPDATED_AT_ASC`, `OrderSort::UPDATED_AT_DESC`
- `OrderSort::SHIPPED_DATE_ASC`, `OrderSort::SHIPPED_DATE_DESC`
- `OrderSort::SHIPPING_DATE_ASC`, `OrderSort::SHIPPING_DATE_DESC` (deprecated)

### Customers (`CustomerSort`)
- `CustomerSort::ID_ASC`, `CustomerSort::ID_DESC` (default)
- `CustomerSort::CREATED_AT_ASC`, `CustomerSort::CREATED_AT_DESC`
- `CustomerSort::UPDATED_AT_ASC`, `CustomerSort::UPDATED_AT_DESC`

### Discounts (`DiscountSort`)
- `DiscountSort::ID_ASC`, `DiscountSort::ID_DESC` (default)
- `DiscountSort::CREATED_AT_ASC`, `DiscountSort::CREATED_AT_DESC`
- `DiscountSort::UPDATED_AT_ASC`, `DiscountSort::UPDATED_AT_DESC`

### Bundles (`BundleSort`)
- `BundleSort::ID_ASC`, `BundleSort::ID_DESC` (default)
- `BundleSort::UPDATED_AT_ASC`, `BundleSort::UPDATED_AT_DESC`

### Metafields (`MetafieldSort`)
- `MetafieldSort::ID_ASC`, `MetafieldSort::ID_DESC` (default)
- `MetafieldSort::UPDATED_AT_ASC`, `MetafieldSort::UPDATED_AT_DESC`

### One-Times (`OneTimeSort`)
- `OneTimeSort::ID_ASC`, `OneTimeSort::ID_DESC` (default)
- `OneTimeSort::CREATED_AT_ASC`, `OneTimeSort::CREATED_AT_DESC`
- `OneTimeSort::UPDATED_AT_ASC`, `OneTimeSort::UPDATED_AT_DESC`

### Products (`ProductSort`)
- `ProductSort::ID_ASC`, `ProductSort::ID_DESC` (default)
- `ProductSort::CREATED_AT_ASC`, `ProductSort::CREATED_AT_DESC`
- `ProductSort::UPDATED_AT_ASC`, `ProductSort::UPDATED_AT_DESC`
- `ProductSort::TITLE_ASC`, `ProductSort::TITLE_DESC`

### Payment Methods (`PaymentMethodSort`)
- `PaymentMethodSort::ID_ASC`, `PaymentMethodSort::ID_DESC` (default)
- `PaymentMethodSort::CREATED_AT_ASC`, `PaymentMethodSort::CREATED_AT_DESC`
- `PaymentMethodSort::UPDATED_AT_ASC`, `PaymentMethodSort::UPDATED_AT_DESC`

### Plans (`PlanSort`)
- `PlanSort::ID_ASC`, `PlanSort::ID_DESC` (default)
- `PlanSort::CREATED_AT_ASC`, `PlanSort::CREATED_AT_DESC`
- `PlanSort::UPDATED_AT_ASC`, `PlanSort::UPDATED_AT_DESC`

Note: Plans are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.

### Webhooks (`WebhookSort`)
- `WebhookSort::ID_ASC`, `WebhookSort::ID_DESC` (default)
- `WebhookSort::CREATED_AT_ASC`, `WebhookSort::CREATED_AT_DESC`
- `WebhookSort::UPDATED_AT_ASC`, `WebhookSort::UPDATED_AT_DESC`

## Usage Examples

```php
use Recharge\Enums\Sort\SubscriptionSort;
use Recharge\Enums\Sort\ChargeSort;

// Using enums (recommended)
foreach ($client->subscriptions()->list(['sort_by' => SubscriptionSort::CREATED_AT_DESC]) as $sub) {
    // ...
}

// Combine sorting with filters
foreach ($client->charges()->list([
    'status' => 'queued',
    'sort_by' => ChargeSort::SCHEDULED_AT_ASC
]) as $charge) {
    // Queued charges sorted by scheduled date (earliest first)
}

// String values also work (for backward compatibility)
foreach ($client->subscriptions()->list(['sort_by' => 'created_at-desc']) as $sub) {
    // ...
}
```
