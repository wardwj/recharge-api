# Payment Methods

Manage customer payment method resources in Recharge (2021-11 only).

**Note:** Payment methods are primarily available in API version 2021-11. Payment sources in 2021-01 are deprecated.

**Permissions Required:** Payment methods require specific API token scopes:
- `read_payment_methods` for read operations
- `write_payment_methods` for create/update/delete operations

## List Payment Methods

```php
// List payment methods
foreach ($client->paymentMethods()->list() as $paymentMethod) {
    echo "Payment Method ID: {$paymentMethod->id}, Type: {$paymentMethod->paymentType?->value}\n";
}

// With sorting
use Recharge\Enums\Sort\PaymentMethodSort;

foreach ($client->paymentMethods()->list(['sort_by' => PaymentMethodSort::CREATED_AT_DESC]) as $paymentMethod) {
    // Payment methods sorted by creation date (newest first)
}

// Filter by customer
foreach ($client->paymentMethods()->list(['customer_id' => 123]) as $paymentMethod) {
    // Payment methods for a specific customer
}

// Include billing addresses
foreach ($client->paymentMethods()->list(['include' => 'addresses']) as $paymentMethod) {
    // Payment methods with billing addresses included
}
```

## Get Single Payment Method

```php
$paymentMethod = $client->paymentMethods()->get(123);

// Check if payment method is valid
if ($paymentMethod->isValid()) {
    echo "Payment method is valid\n";
}

// Get last 4 digits (for credit cards)
if ($paymentMethod->isCreditCard()) {
    echo "Last 4: {$paymentMethod->getLast4()}\n";
}
```

## Create Payment Method

```php
use Recharge\Enums\PaymentType;
use Recharge\Enums\ProcessorName;

$paymentMethod = $client->paymentMethods()->create([
    'customer_id' => 123,
    'payment_type' => PaymentType::CREDIT_CARD->value,
    'processor_name' => ProcessorName::STRIPE->value,
    'processor_customer_token' => 'cus_abc123',
    'processor_payment_method_token' => 'pm_xyz789',
    'default' => true, // Set as default (will unset other defaults for this customer)
    'billing_address' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Main St',
        'city' => 'New York',
        'province' => 'NY',
        'zip' => '10001',
        'country_code' => 'US',
    ],
]);
```

## Update Payment Method

```php
// Update a payment method (limited - typically only default and billing_address)
$client->paymentMethods()->update(123, [
    'default' => true, // Set as default
    'billing_address' => [
        'address1' => '456 Oak Ave',
        'city' => 'Los Angeles',
    ],
]);
```

## Delete Payment Method

```php
// Delete a payment method (only if not in use by active subscriptions)
$client->paymentMethods()->delete(123);
```

**Payment Types:**
- `PaymentType::CREDIT_CARD` - Credit card
- `PaymentType::PAYPAL` - PayPal
- `PaymentType::APPLE_PAY` - Apple Pay
- `PaymentType::GOOGLE_PAY` - Google Pay
- `PaymentType::SEPA_DEBIT` - SEPA Direct Debit

**Processor Names:**
- `ProcessorName::STRIPE` - Stripe
- `ProcessorName::BRAINTREE` - Braintree
- `ProcessorName::AUTHORIZE` - Authorize.net
- `ProcessorName::SHOPIFY_PAYMENTS` - Shopify Payments (read-only)
- `ProcessorName::MOLLIE` - Mollie

**Important Notes:**
- Only one payment method can be set as default per customer
- Many fields cannot be updated (e.g., card number, expiry) - create a new payment method instead
- For `shopify_payments` processor, updates are read-only (managed by Shopify)
- Payment methods can only be deleted if not in use by active subscriptions

See [Sorting Documentation](sorting.md) for available sort options.
