<?php

/**
 * Example script for testing the Recharge API client
 *
 * Usage:
 *   php example.php YOUR_API_TOKEN
 *
 * This script demonstrates basic usage of the Recharge API client
 * with both API versions (2021-01 and 2021-11).
 */

require_once __DIR__ . '/vendor/autoload.php';

use Recharge\Client;
use Recharge\Exceptions\RechargeException;

// Get API token from command line argument
$apiToken = $argv[1] ?? getenv('RECHARGE_API_TOKEN');

if (!$apiToken) {
    echo "Error: Please provide your Recharge API token as an argument or set RECHARGE_API_TOKEN environment variable.\n";
    echo "Usage: php example.php YOUR_API_TOKEN\n";
    exit(1);
}

try {
    // Initialize client with default version (2021-11)
    echo "=== Testing Recharge API Client ===\n\n";
    
    $client = new Client($apiToken);
    echo "✓ Client initialized with API version: " . $client->getApiVersion() . "\n\n";
    
    // Test 1: Get store information (safe endpoint)
    echo "--- Test 1: Get Store Information ---\n";
    try {
        $store = $client->store()->get();
        echo "✓ Store retrieved successfully\n";
        echo "  Store ID: " . $store->getId() . "\n";
        echo "  Store Name: " . ($store->getName() ?? 'N/A') . "\n";
        echo "  Store Domain: " . ($store->getDomain() ?? 'N/A') . "\n";
        echo "  DTO Class: " . get_class($store) . "\n";
    } catch (RechargeException $e) {
        echo "✗ Error retrieving store: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 2: Test version switching
    echo "--- Test 2: API Version Switching ---\n";
    echo "  Current version: " . $client->getApiVersion() . "\n";
    $client->setApiVersion(Client::API_VERSION_2021_01);
    echo "  Switched to: " . $client->getApiVersion() . "\n";
    try {
        $store = $client->store()->get();
        echo "  DTO Class with 2021-01: " . get_class($store) . "\n";
    } catch (RechargeException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    $client->setApiVersion(Client::API_VERSION_2021_11);
    echo "  Switched back to: " . $client->getApiVersion() . "\n\n";
    
    // Test 3: List customers (with limit)
    echo "--- Test 3: List Customers (limit 5) ---\n";
    try {
        $customers = $client->customers()->list(['limit' => 5]);
        echo "✓ Retrieved " . count($customers) . " customer(s)\n";
        if (!empty($customers)) {
            $customer = $customers[0];
            echo "  First Customer ID: " . $customer->getId() . "\n";
            echo "  First Customer Email: " . ($customer->getEmail() ?? 'N/A') . "\n";
            echo "  DTO Class: " . get_class($customer) . "\n";
        }
    } catch (RechargeException $e) {
        echo "✗ Error listing customers: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 4: List subscriptions (with limit)
    echo "--- Test 4: List Subscriptions (limit 5) ---\n";
    try {
        $subscriptions = $client->subscriptions()->list(['limit' => 5]);
        echo "✓ Retrieved " . count($subscriptions) . " subscription(s)\n";
        if (!empty($subscriptions)) {
            $subscription = $subscriptions[0];
            echo "  First Subscription ID: " . $subscription->getId() . "\n";
            echo "  First Subscription Status: " . ($subscription->getStatus() ?? 'N/A') . "\n";
            echo "  DTO Class: " . get_class($subscription) . "\n";
        }
    } catch (RechargeException $e) {
        echo "✗ Error listing subscriptions: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 5: List charges (with limit)
    echo "--- Test 5: List Charges (limit 5) ---\n";
    try {
        $charges = $client->charges()->list(['limit' => 5]);
        echo "✓ Retrieved " . count($charges) . " charge(s)\n";
        if (!empty($charges)) {
            $charge = $charges[0];
            echo "  First Charge ID: " . $charge->getId() . "\n";
            echo "  First Charge Status: " . ($charge->getStatus() ?? 'N/A') . "\n";
            echo "  DTO Class: " . get_class($charge) . "\n";
        }
    } catch (RechargeException $e) {
        echo "✗ Error listing charges: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // Test 6: Verify DTO factory works correctly
    echo "--- Test 6: DTO Factory Version Detection ---\n";
    echo "  Testing with 2021-01 version:\n";
    $client2021_01 = new Client($apiToken, Client::API_VERSION_2021_01);
    try {
        $store = $client2021_01->store()->get();
        $expectedClass = "Recharge\\DTO\\V2021_01\\Store";
        $actualClass = get_class($store);
        if ($actualClass === $expectedClass) {
            echo "  ✓ Correct DTO class returned: " . $actualClass . "\n";
        } else {
            echo "  ✗ Wrong DTO class. Expected: $expectedClass, Got: $actualClass\n";
        }
    } catch (RechargeException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    
    echo "  Testing with 2021-11 version:\n";
    $client2021_11 = new Client($apiToken, Client::API_VERSION_2021_11);
    try {
        $store = $client2021_11->store()->get();
        $expectedClass = "Recharge\\DTO\\V2021_11\\Store";
        $actualClass = get_class($store);
        if ($actualClass === $expectedClass) {
            echo "  ✓ Correct DTO class returned: " . $actualClass . "\n";
        } else {
            echo "  ✗ Wrong DTO class. Expected: $expectedClass, Got: $actualClass\n";
        }
    } catch (RechargeException $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    echo "=== All Tests Completed ===\n";
    
} catch (RechargeException $e) {
    echo "\n✗ Fatal Error: " . $e->getMessage() . "\n";
    if ($e->getCode() !== 0) {
        echo "  Status Code: " . $e->getCode() . "\n";
    }
    exit(1);
} catch (\Exception $e) {
    echo "\n✗ Unexpected Error: " . $e->getMessage() . "\n";
    exit(1);
}
