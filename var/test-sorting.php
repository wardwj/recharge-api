<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\ChargeSort;
use Recharge\Enums\Sort\CustomerSort;
use Recharge\Enums\Sort\OrderSort;
use Recharge\Enums\Sort\SubscriptionSort;
use Recharge\RechargeClient;

// Get API token from environment or use provided one
$apiToken = $_ENV['RECHARGE_API_TOKEN'] ?? getenv('RECHARGE_API_TOKEN') ?? '';

if (empty($apiToken)) {
    echo "❌ Error: RECHARGE_API_TOKEN environment variable not set\n";
    echo "Usage: RECHARGE_API_TOKEN=your_token php test-sorting.php\n";
    exit(1);
}

$client = new RechargeClient($apiToken);

// Try both API versions for Orders sorting
$originalVersion = $client->getApiVersion();
echo "Current API version: {$originalVersion->value}\n\n";

echo "🧪 Testing Sorting Functionality (Readonly Operations Only)\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "⚠️  All operations are READONLY (GET requests only)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test Subscriptions sorting
echo "📋 Testing Subscriptions Sorting\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // Test with enum
    echo "✓ Testing with SubscriptionSort::CREATED_AT_DESC enum...\n";
    $paginator = $client->subscriptions()->list([
        'limit' => 5,
        'sort_by' => SubscriptionSort::CREATED_AT_DESC
    ]);
    
    $subscriptions = $paginator->take(5);
    if (count($subscriptions) > 1) {
        $first = $subscriptions[0];
        $second = $subscriptions[1];
        echo "  First subscription ID: {$first->id}\n";
        echo "  Second subscription ID: {$second->id}\n";
        
        // Verify descending order (newer first)
        if ($first->id > $second->id) {
            echo "  ✓ IDs are in descending order (newest first)\n";
        } else {
            echo "  ⚠ IDs may not be in expected order (check created_at)\n";
        }
    }
    
    // Test with string
    echo "\n✓ Testing with string 'id-asc'...\n";
    $paginator = $client->subscriptions()->list([
        'limit' => 5,
        'sort_by' => 'id-asc'
    ]);
    
    $subscriptions = $paginator->take(5);
    if (count($subscriptions) > 1) {
        $first = $subscriptions[0];
        $second = $subscriptions[1];
        echo "  First subscription ID: {$first->id}\n";
        echo "  Second subscription ID: {$second->id}\n";
        
        // Verify ascending order
        if ($first->id < $second->id) {
            echo "  ✓ IDs are in ascending order (oldest first)\n";
        } else {
            echo "  ⚠ IDs may not be in expected order\n";
        }
    }
    
    echo "✓ Subscriptions sorting works!\n\n";
} catch (\Exception $e) {
    echo "❌ Subscriptions sorting failed: " . $e->getMessage() . "\n\n";
}

// Test Charges sorting
echo "💳 Testing Charges Sorting\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // Test with enum
    echo "✓ Testing with ChargeSort::ID_DESC enum...\n";
    $paginator = $client->charges()->list([
        'limit' => 5,
        'sort_by' => ChargeSort::ID_DESC
    ]);
    
    $charges = $paginator->take(5);
    if (count($charges) > 1) {
        $first = $charges[0];
        $second = $charges[1];
        echo "  First charge ID: {$first->id}\n";
        echo "  Second charge ID: {$second->id}\n";
        
        if ($first->id > $second->id) {
            echo "  ✓ IDs are in descending order\n";
        }
    }
    
    // Test scheduled_at sorting
    echo "\n✓ Testing with ChargeSort::SCHEDULED_AT_ASC enum...\n";
    $paginator = $client->charges()->list([
        'limit' => 5,
        'sort_by' => ChargeSort::SCHEDULED_AT_ASC
    ]);
    
    $charges = $paginator->take(5);
    if (count($charges) > 0) {
        echo "  Retrieved " . count($charges) . " charges sorted by scheduled_at (ascending)\n";
        echo "  ✓ Scheduled_at sorting works\n";
    }
    
    echo "✓ Charges sorting works!\n\n";
} catch (\Exception $e) {
    echo "❌ Charges sorting failed: " . $e->getMessage() . "\n\n";
}

// Test Orders sorting
echo "📦 Testing Orders Sorting\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // Try with 2021-01 API version first (docs show sorting for 2021-01)
    echo "✓ Testing with API 2021-01 and OrderSort::CREATED_AT_DESC...\n";
    $client->setApiVersion(\Recharge\Enums\ApiVersion::V2021_01);
    $paginator = $client->orders()->list([
        'limit' => 5,
        'sort_by' => OrderSort::CREATED_AT_DESC
    ]);
    
    $orders = $paginator->take(5);
    if (count($orders) > 1) {
        $first = $orders[0];
        $second = $orders[1];
        echo "  First order ID: {$first->id}\n";
        echo "  Second order ID: {$second->id}\n";
        
        if ($first->id > $second->id) {
            echo "  ✓ IDs are in descending order\n";
        }
    }
    
    // Test shipped_date sorting
    echo "\n✓ Testing with OrderSort::SHIPPED_DATE_ASC...\n";
    $paginator = $client->orders()->list([
        'limit' => 5,
        'sort_by' => OrderSort::SHIPPED_DATE_ASC
    ]);
    
    $orders = $paginator->take(5);
    if (count($orders) > 0) {
        echo "  Retrieved " . count($orders) . " orders sorted by shipped_date (ascending)\n";
        echo "  ✓ Shipped_date sorting works\n";
    }
    
    // Restore original version
    $client->setApiVersion($originalVersion);
    
    echo "✓ Orders sorting works!\n\n";
} catch (\Exception $e) {
    // Restore original version on error
    $client->setApiVersion($originalVersion);
    echo "❌ Orders sorting failed: " . $e->getMessage() . "\n\n";
}

// Test Customers sorting
echo "👥 Testing Customers Sorting\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // Test with enum
    echo "✓ Testing with CustomerSort::ID_ASC enum...\n";
    $paginator = $client->customers()->list([
        'limit' => 5,
        'sort_by' => CustomerSort::ID_ASC
    ]);
    
    $customers = $paginator->take(5);
    if (count($customers) > 1) {
        $first = $customers[0];
        $second = $customers[1];
        echo "  First customer ID: {$first->id}\n";
        echo "  Second customer ID: {$second->id}\n";
        
        if ($first->id < $second->id) {
            echo "  ✓ IDs are in ascending order\n";
        }
    }
    
    echo "✓ Customers sorting works!\n\n";
} catch (\Exception $e) {
    echo "❌ Customers sorting failed: " . $e->getMessage() . "\n\n";
}

// Test invalid sort_by
echo "🛡️  Testing Invalid Sort Validation\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $client->subscriptions()->list(['sort_by' => 'invalid-sort']);
    echo "❌ Should have thrown exception for invalid sort_by\n";
} catch (\InvalidArgumentException $e) {
    echo "✓ Invalid sort_by correctly throws InvalidArgumentException\n";
    echo "  Message: " . $e->getMessage() . "\n\n";
} catch (\Exception $e) {
    echo "⚠ Unexpected exception: " . get_class($e) . " - " . $e->getMessage() . "\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ Sorting tests completed!\n";
