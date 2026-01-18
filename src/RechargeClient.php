<?php

declare(strict_types=1);

namespace Recharge;

use Psr\Log\LoggerAwareInterface;
use Recharge\Concerns\HasLogging;
use Recharge\Config\RechargeConfig;
use Recharge\Contracts\ClientInterface;
use Recharge\Enums\ApiVersion;
use Recharge\Exceptions\RechargeException;
use Recharge\Http\Connector;
use Recharge\Http\HttpClientFactory;

/**
 * Main client for Recharge API
 *
 * This class provides a fluent interface to interact with the Recharge Payments API.
 * It supports both API versions 2021-01 and 2021-11.
 *
 * Implements PSR-3 LoggerAwareInterface for optional logging support.
 * Use setLogger() to enable request/response logging for debugging.
 *
 * @see https://developer.rechargepayments.com/2021-11/
 */
class RechargeClient implements ClientInterface, LoggerAwareInterface
{
    use HasLogging;

    /**
     * @var Connector HTTP connector instance
     */
    private Connector $connector;

    /**
     * @var RechargeConfig Configuration instance
     */
    private RechargeConfig $config;

    /**
     * @var array<string, Resources\AbstractResource|Resources\Customers|Resources\Subscriptions|Resources\Orders|Resources\Charges|Resources\Addresses|Resources\Products|Resources\Discounts|Resources\Bundles|Resources\Checkouts|Resources\Store> Cached resource instances
     */
    private array $resources = [];

    /**
     * Client constructor
     *
     * @param string $accessToken Your Recharge API access token
     * @param ApiVersion|string|null $apiVersion API version to use (default: 2021-11)
     * @throws RechargeException If the API version is invalid
     */
    public function __construct(string $accessToken, ApiVersion|string|null $apiVersion = null)
    {
        // Convert string to enum if needed (for backward compatibility)
        if ($apiVersion === null) {
            $apiVersion = ApiVersion::default();
        } elseif (is_string($apiVersion)) {
            $apiVersion = ApiVersion::from($apiVersion);
        }

        $this->config = new RechargeConfig($accessToken, $apiVersion);
        $this->initializeConnector();
    }

    /**
     * Create client from config
     *
     * @param RechargeConfig $config Configuration instance
     */
    public static function fromConfig(RechargeConfig $config): self
    {
        $client = new self($config->getAccessToken(), $config->getApiVersion());
        $client->config = $config;
        $client->initializeConnector();

        return $client;
    }

    /**
     * Set the API version
     *
     * @param ApiVersion|string $apiVersion API version
     * @return self Returns instance for method chaining
     * @throws RechargeException If the API version is invalid
     */
    public function setApiVersion(ApiVersion|string $apiVersion): self
    {
        if (is_string($apiVersion)) {
            $apiVersion = ApiVersion::from($apiVersion);
        }

        $this->config = $this->config->withApiVersion($apiVersion);
        $this->initializeConnector();

        return $this;
    }

    /**
     * Get the current API version
     */
    public function getApiVersion(): ApiVersion
    {
        return $this->config->getApiVersion();
    }

    /**
     * Get the configuration
     */
    public function getConfig(): RechargeConfig
    {
        return $this->config;
    }

    /**
     * Make a GET request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $query Query parameters
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function get(string $endpoint, array $query = []): array
    {
        $response = $this->connector->get($endpoint, $query);

        return is_array($response) ? $response : $response->body;
    }

    /**
     * Make a POST request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->connector->post($endpoint, $data);
    }

    /**
     * Make a PUT request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->connector->put($endpoint, $data);
    }

    /**
     * Make a DELETE request to the API
     *
     * @param string $endpoint API endpoint path
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function delete(string $endpoint): array
    {
        return $this->connector->delete($endpoint);
    }

    /**
     * Get HTTP connector instance
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * Initialize the HTTP connector
     */
    private function initializeConnector(): void
    {
        $httpClient = HttpClientFactory::createClient();
        $requestFactory = HttpClientFactory::createRequestFactory();
        $streamFactory = HttpClientFactory::createStreamFactory();

        $this->connector = new Connector(
            $httpClient,
            $requestFactory,
            $streamFactory,
            $this->config
        );
    }

    /**
     * Get Customers resource instance
     *
     * @return Resources\Customers Customers resource
     */
    public function customers(): Resources\Customers
    {
        if (!isset($this->resources['customers'])) {
            $this->resources['customers'] = new Resources\Customers($this);
        }

        return $this->resources['customers'];
    }

    /**
     * Get Subscriptions resource instance
     *
     * @return Resources\Subscriptions Subscriptions resource
     */
    public function subscriptions(): Resources\Subscriptions
    {
        if (!isset($this->resources['subscriptions'])) {
            $this->resources['subscriptions'] = new Resources\Subscriptions($this);
        }

        return $this->resources['subscriptions'];
    }

    /**
     * Get Orders resource instance
     *
     * @return Resources\Orders Orders resource
     */
    public function orders(): Resources\Orders
    {
        if (!isset($this->resources['orders'])) {
            $this->resources['orders'] = new Resources\Orders($this);
        }

        return $this->resources['orders'];
    }

    /**
     * Get Charges resource instance
     *
     * @return Resources\Charges Charges resource
     */
    public function charges(): Resources\Charges
    {
        if (!isset($this->resources['charges'])) {
            $this->resources['charges'] = new Resources\Charges($this);
        }

        return $this->resources['charges'];
    }

    /**
     * Get Addresses resource instance
     *
     * @return Resources\Addresses Addresses resource
     */
    public function addresses(): Resources\Addresses
    {
        if (!isset($this->resources['addresses'])) {
            $this->resources['addresses'] = new Resources\Addresses($this);
        }

        return $this->resources['addresses'];
    }

    /**
     * Get Products resource instance
     *
     * @return Resources\Products Products resource
     */
    public function products(): Resources\Products
    {
        if (!isset($this->resources['products'])) {
            $this->resources['products'] = new Resources\Products($this);
        }

        return $this->resources['products'];
    }

    /**
     * Get Discounts resource instance
     *
     * @return Resources\Discounts Discounts resource
     */
    public function discounts(): Resources\Discounts
    {
        if (!isset($this->resources['discounts'])) {
            $this->resources['discounts'] = new Resources\Discounts($this);
        }

        /** @var Resources\Discounts */
        return $this->resources['discounts'];
    }

    /**
     * Get Bundles resource instance
     *
     * @return Resources\Bundles Bundles resource
     */
    public function bundles(): Resources\Bundles
    {
        if (!isset($this->resources['bundles'])) {
            $this->resources['bundles'] = new Resources\Bundles($this);
        }

        /** @var Resources\Bundles */
        return $this->resources['bundles'];
    }

    /**
     * Get Checkouts resource instance
     *
     * @return Resources\Checkouts Checkouts resource
     */
    public function checkouts(): Resources\Checkouts
    {
        if (!isset($this->resources['checkouts'])) {
            $this->resources['checkouts'] = new Resources\Checkouts($this);
        }

        /** @var Resources\Checkouts */
        return $this->resources['checkouts'];
    }

    /**
     * Get Store resource instance
     *
     * @return Resources\Store Store resource
     */
    public function store(): Resources\Store
    {
        if (!isset($this->resources['store'])) {
            $this->resources['store'] = new Resources\Store($this);
        }

        return $this->resources['store'];
    }
}
