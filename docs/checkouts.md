# Checkouts

Manage checkout resources in Recharge.

**Note:** Checkouts are only available for BigCommerce and Custom setups. Not supported for Shopify stores (deprecated as of October 18, 2024). Requires Pro or Custom plan.

## Create Checkout

```php
// Create a checkout
$checkout = $client->checkouts()->create([
    'email' => 'customer@example.com',
    'line_items' => [
        [
            'external_product_id' => ['ecommerce' => 'bigcommerce', 'product_id' => '123'],
            'external_variant_id' => ['ecommerce' => 'bigcommerce', 'variant_id' => '456'],
            'quantity' => 2,
        ],
    ],
    'billing_address' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'address1' => '123 Main St',
        'city' => 'New York',
        'province' => 'NY',
        'zip' => '10001',
        'country' => 'United States',
        'country_code' => 'US',
    ],
]);
```

## Get Single Checkout

```php
$checkout = $client->checkouts()->get('checkout_token_123');
```

## Update Checkout

```php
$checkout = $client->checkouts()->update('checkout_token_123', [
    'shipping_address' => [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'address1' => '456 Oak Ave',
    ],
]);
```

## Get Shipping Rates

```php
$shippingRates = $client->checkouts()->getShippingRates('checkout_token_123');
```

## Process/Charge Checkout

```php
// Process/charge a checkout
$checkout = $client->checkouts()->charge('checkout_token_123', [
    'payment_method' => [
        'type' => 'credit_card',
        'gateway' => 'stripe',
    ],
]);
// After processing, $checkout->chargeId will be set
```

**Note:** Checkouts are only available in API version 2021-11. The SDK automatically switches to 2021-11 when needed.
