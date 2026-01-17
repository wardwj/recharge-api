<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Recharge\Enums\ApiVersion;
use Recharge\Enums\Sort\BundleSort;
use Recharge\RechargeClient;

// Get API token from environment or use provided one
$apiToken = $_ENV['RECHARGE_API_TOKEN'] ?? getenv('RECHARGE_API_TOKEN') ?? '';

if (empty($apiToken)) {
    echo "❌ Error: RECHARGE_API_TOKEN environment variable not set\n";
    echo "Usage: RECHARGE_API_TOKEN=your_token php var/test-bundles.php\n";
    exit(1);
}

$client = new RechargeClient($apiToken);

echo "🧪 Testing Bundle Selections Resource (Readonly Operations Only)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: List bundles (both versions)
echo "1. Testing list() - API Version: {$client->getApiVersion()->value}\n";
try {
    $count = 0;
    $maxItems = 5; // Limit for testing
    
    foreach ($client->bundles()->list(['limit' => 10]) as $bundle) {
        echo "  - Bundle ID: {$bundle->id}\n";
        echo "    Title: " . ($bundle->title ?? 'N/A') . "\n";
        echo "    Handle: " . ($bundle->handle ?? 'N/A') . "\n";
        echo "    Description: " . ($bundle->description ?? 'N/A') . "\n";
        echo "    Products: " . (is_array($bundle->products) ? count($bundle->products) . ' items' : 'N/A') . "\n";
        echo "    Created: " . ($bundle->createdAt?->toIso8601String() ?? 'N/A') . "\n";
        echo "    Updated: " . ($bundle->updatedAt?->toIso8601String() ?? 'N/A') . "\n";
        echo "    Raw Data Keys: " . implode(', ', array_keys($bundle->rawData)) . "\n";
        echo "\n";
        
        $count++;
        if ($count >= $maxItems) {
            echo "  (Limited to {$maxItems} items for testing)\n";
            break;
        }
    }
    
    if ($count === 0) {
        echo "  No bundles found\n";
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
    echo "  " . get_class($e) . "\n";
}

echo "\n";

// Test 2: Test with 2021-01 API version
echo "2. Testing list() - API Version: 2021-01\n";
try {
    $client->setApiVersion(ApiVersion::V2021_01);
    $count = 0;
    $maxItems = 3;
    
    foreach ($client->bundles()->list(['limit' => 5]) as $bundle) {
        echo "  - Bundle ID: {$bundle->id}\n";
        echo "    Raw Data Keys: " . implode(', ', array_keys($bundle->rawData)) . "\n";
        echo "\n";
        
        $count++;
        if ($count >= $maxItems) {
            break;
        }
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

// Reset to 2021-11
$client->setApiVersion(ApiVersion::V2021_11);

echo "\n";

// Test 3: Test sorting
echo "3. Testing sorting with BundleSort enum\n";
try {
    $count = 0;
    foreach ($client->bundles()->list(['sort_by' => BundleSort::UPDATED_AT_DESC, 'limit' => 3]) as $bundle) {
        echo "  - Bundle ID: {$bundle->id}, Created: " . ($bundle->createdAt?->toIso8601String() ?? 'N/A') . "\n";
        $count++;
        if ($count >= 3) {
            break;
        }
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Get a specific bundle (if we have one)
echo "4. Testing get() method\n";
try {
    // First, try to get a bundle ID from listing
    $firstBundle = null;
    foreach ($client->bundles()->list(['limit' => 1]) as $bundle) {
        $firstBundle = $bundle;
        break;
    }
    
    if ($firstBundle) {
        $bundleId = $firstBundle->id;
        echo "  Fetching bundle ID: {$bundleId}\n";
        $bundle = $client->bundles()->get($bundleId);
        echo "  - Bundle ID: {$bundle->id}\n";
        echo "    Title: " . ($bundle->title ?? 'N/A') . "\n";
        echo "    Handle: " . ($bundle->handle ?? 'N/A') . "\n";
        echo "    Description: " . ($bundle->description ?? 'N/A') . "\n";
        echo "    Raw Data Keys: " . implode(', ', array_keys($bundle->rawData)) . "\n";
        echo "\n";
        echo "  Full Raw Data (first bundle):\n";
        echo "  " . json_encode($bundle->rawData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo "  No bundles available to test get() method\n";
    }
} catch (\Exception $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
    echo "  " . get_class($e) . "\n";
}

echo "\n=== Testing Complete ===\n";
