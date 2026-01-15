<?php

namespace Recharge\Tests;

use PHPUnit\Framework\TestCase;
use Recharge\Client;
use Recharge\DTO\DTOFactory;
use Recharge\Exceptions\RechargeException;

/**
 * Basic tests for Recharge API Client
 *
 * These tests require a valid API token and actual API access.
 * Set RECHARGE_API_TOKEN environment variable before running.
 */
class ClientTest extends TestCase
{
    private ?Client $client = null;
    private ?string $apiToken = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->apiToken = getenv('RECHARGE_API_TOKEN');
        
        if (!$this->apiToken) {
            $this->markTestSkipped('RECHARGE_API_TOKEN environment variable is not set');
        }
        
        $this->client = new Client($this->apiToken);
    }

    public function testClientInitialization(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
        $this->assertEquals(Client::API_VERSION_2021_11, $this->client->getApiVersion());
    }

    public function testApiVersionSwitching(): void
    {
        $this->client->setApiVersion(Client::API_VERSION_2021_01);
        $this->assertEquals(Client::API_VERSION_2021_01, $this->client->getApiVersion());
        
        $this->client->setApiVersion(Client::API_VERSION_2021_11);
        $this->assertEquals(Client::API_VERSION_2021_11, $this->client->getApiVersion());
    }

    public function testInvalidApiVersion(): void
    {
        $this->expectException(RechargeException::class);
        $this->expectExceptionMessage('Unsupported API version');
        
        $this->client->setApiVersion('invalid-version');
    }

    public function testStoreRetrieval(): void
    {
        $store = $this->client->store()->get();
        
        $this->assertIsObject($store);
        $this->assertIsInt($store->getId());
    }

    public function testVersionedDtos2021_01(): void
    {
        $client = new Client($this->apiToken, Client::API_VERSION_2021_01);
        $store = $client->store()->get();
        
        $this->assertStringContainsString('V2021_01', get_class($store));
    }

    public function testVersionedDtos2021_11(): void
    {
        $client = new Client($this->apiToken, Client::API_VERSION_2021_11);
        $store = $client->store()->get();
        
        $this->assertStringContainsString('V2021_11', get_class($store));
    }

    public function testListCustomers(): void
    {
        $customers = $this->client->customers()->list(['limit' => 5]);
        
        $this->assertIsArray($customers);
        
        if (!empty($customers)) {
            $customer = $customers[0];
            $this->assertIsObject($customer);
            $this->assertIsInt($customer->getId());
        }
    }

    public function testListSubscriptions(): void
    {
        $subscriptions = $this->client->subscriptions()->list(['limit' => 5]);
        
        $this->assertIsArray($subscriptions);
        
        if (!empty($subscriptions)) {
            $subscription = $subscriptions[0];
            $this->assertIsObject($subscription);
            $this->assertIsInt($subscription->getId());
        }
    }

    public function testListCharges(): void
    {
        $charges = $this->client->charges()->list(['limit' => 5]);
        
        $this->assertIsArray($charges);
        
        if (!empty($charges)) {
            $charge = $charges[0];
            $this->assertIsObject($charge);
            $this->assertIsInt($charge->getId());
        }
    }
}
