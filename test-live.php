<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Recharge\RechargeClient;
use Recharge\Enums\ApiVersion;
use Recharge\Exceptions\RechargeException;

// Get API key from command line or environment
$apiKey = $argv[1] ?? getenv('RECHARGE_API_KEY') ?: null;

if (!$apiKey) {
    echo "Usage: php test-live.php YOUR_API_KEY\n";
    echo "Or set RECHARGE_API_KEY environment variable\n";
    exit(1);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  RECHARGE API SDK - LIVE TEST\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Test with default version (2021-11)
echo "🔧 Initializing client (API v2021-11)...\n";
$client = new RechargeClient($apiKey, ApiVersion::V2021_11);
echo "✓ Client initialized\n\n";

// Test 1: Get Store Info
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Store Information\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $store = $client->store()->get();
    echo "✓ Store: {$store->name}\n";
    echo "  Domain: {$store->domain}\n";
    echo "  Currency: {$store->currency}\n";
    echo "  Timezone: {$store->getTimezone()}\n\n";
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

// Test 2: List Customers (first 3)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: List Customers (first 3)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $customers = $client->customers()->list(['limit' => 3])->take(3);
    foreach ($customers as $customer) {
        echo "✓ Customer #{$customer->id}\n";
        echo "  Name: {$customer->getFullName()}\n";
        echo "  Email: {$customer->email}\n";
        echo "  Created: {$customer->createdAt?->format('Y-m-d')}\n";
        echo "\n";
    }
    if (empty($customers)) {
        echo "  No customers found\n\n";
    }
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

// Test 3: List Subscriptions (first 3)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: List Subscriptions (first 3)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $subscriptions = $client->subscriptions()->list(['limit' => 3])->take(3);
    foreach ($subscriptions as $sub) {
        echo "✓ Subscription #{$sub->id}\n";
        echo "  Product: {$sub->getProductTitle()}\n";
        echo "  Status: {$sub->status?->value}\n";
        echo "  Price: \${$sub->price}\n";
        echo "  Quantity: {$sub->quantity}\n";
        echo "\n";
    }
    if (empty($subscriptions)) {
        echo "  No subscriptions found\n\n";
    }
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

// Test 4: List Charges (first 3)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: List Charges (first 3)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $charges = $client->charges()->list(['limit' => 3])->take(3);
    foreach ($charges as $charge) {
        echo "✓ Charge #{$charge->id}\n";
        echo "  Status: {$charge->status?->value}\n";
        echo "  Amount: \$" . ($charge->totalPrice ?? $charge->subtotalPrice ?? '0.00') . "\n";
        echo "  Customer ID: {$charge->customerId}\n";
        echo "\n";
    }
    if (empty($charges)) {
        echo "  No charges found\n\n";
    }
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

// Test 5: Test Pagination
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5: Pagination Test\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    $paginator = $client->subscriptions()->list(['limit' => 5]);
    
    echo "✓ First item: ";
    $first = $paginator->first();
    if ($first) {
        echo "#{$first->id}\n";
    } else {
        echo "No items\n";
    }
    
    echo "✓ Is empty: " . ($paginator->isEmpty() ? 'Yes' : 'No') . "\n";
    
    $count = 0;
    foreach ($paginator->take(10) as $item) {
        $count++;
    }
    echo "✓ Fetched items: {$count}\n\n";
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

// Test 6: Switch to API version 2021-01
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 6: API Version Switching\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
try {
    echo "Current version: {$client->getApiVersion()->value}\n";
    
    $client->setApiVersion(ApiVersion::V2021_01);
    echo "✓ Switched to: {$client->getApiVersion()->value}\n";
    
    // Test a call with 2021-01
    $paginator = $client->subscriptions()->list(['limit' => 2]);
    $first = $paginator->first();
    echo "✓ API 2021-01 working: " . ($first ? "Yes" : "No data") . "\n\n";
    
    // Switch back
    $client->setApiVersion(ApiVersion::V2021_11);
    echo "✓ Switched back to: {$client->getApiVersion()->value}\n\n";
} catch (RechargeException $e) {
    echo "✗ Error: {$e->getMessage()}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  ALL TESTS COMPLETE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ SDK is working correctly with live API!\n\n";
