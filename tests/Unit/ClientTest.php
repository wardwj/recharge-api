<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Config\RechargeConfig;
use Recharge\Contracts\ClientInterface;
use Recharge\Enums\ApiVersion;
use Recharge\Exceptions\ValidationException;
use Recharge\RechargeClient;

/**
 * Unit tests for Recharge API Client
 *
 * These tests do not require API access - they test client initialization
 * and configuration without making actual HTTP requests.
 */
class ClientTest extends TestCase
{
    private string $testToken = 'test_token_12345';

    public function testClientImplementsInterface(): void
    {
        $client = new RechargeClient($this->testToken);

        $this->assertInstanceOf(ClientInterface::class, $client);
    }

    public function testClientInitializationWithDefaultVersion(): void
    {
        $client = new RechargeClient($this->testToken);

        $this->assertEquals(ApiVersion::V2021_11, $client->getApiVersion());
    }

    public function testClientInitializationWithSpecificVersion(): void
    {
        $client = new RechargeClient($this->testToken, ApiVersion::V2021_01);

        $this->assertEquals(ApiVersion::V2021_01, $client->getApiVersion());
    }

    public function testClientInitializationWithStringVersion(): void
    {
        $client = new RechargeClient($this->testToken, '2021-01');

        $this->assertEquals(ApiVersion::V2021_01, $client->getApiVersion());
    }

    public function testClientThrowsExceptionForEmptyToken(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('API access token cannot be empty');

        new RechargeClient('');
    }

    public function testClientThrowsExceptionForInvalidVersion(): void
    {
        $this->expectException(\ValueError::class);

        new RechargeClient($this->testToken, 'invalid-version');
    }

    public function testApiVersionSwitching(): void
    {
        $client = new RechargeClient($this->testToken, ApiVersion::V2021_11);

        $this->assertEquals(ApiVersion::V2021_11, $client->getApiVersion());

        $client->setApiVersion(ApiVersion::V2021_01);
        $this->assertEquals(ApiVersion::V2021_01, $client->getApiVersion());

        $client->setApiVersion('2021-11');
        $this->assertEquals(ApiVersion::V2021_11, $client->getApiVersion());
    }

    public function testClientFromConfig(): void
    {
        $config = new RechargeConfig(
            accessToken: $this->testToken,
            apiVersion: ApiVersion::V2021_01
        );

        $client = RechargeClient::fromConfig($config);

        $this->assertEquals(ApiVersion::V2021_01, $client->getApiVersion());
        $this->assertSame($config, $client->getConfig());
    }

    public function testClientResourceAccessors(): void
    {
        $client = new RechargeClient($this->testToken);

        $this->assertInstanceOf(\Recharge\Resources\Subscriptions::class, $client->subscriptions());
        $this->assertInstanceOf(\Recharge\Resources\Customers::class, $client->customers());
        $this->assertInstanceOf(\Recharge\Resources\Addresses::class, $client->addresses());
        $this->assertInstanceOf(\Recharge\Resources\Charges::class, $client->charges());
        $this->assertInstanceOf(\Recharge\Resources\Orders::class, $client->orders());
        $this->assertInstanceOf(\Recharge\Resources\Products::class, $client->products());
        $this->assertInstanceOf(\Recharge\Resources\Store::class, $client->store());
    }

    public function testClientResourceCaching(): void
    {
        $client = new RechargeClient($this->testToken);

        $subscriptions1 = $client->subscriptions();
        $subscriptions2 = $client->subscriptions();

        // Should return same instance (cached)
        $this->assertSame($subscriptions1, $subscriptions2);
    }

    public function testClientImplementsLoggerAware(): void
    {
        $client = new RechargeClient($this->testToken);

        $this->assertInstanceOf(\Psr\Log\LoggerAwareInterface::class, $client);
    }
}
