<?php

namespace Recharge;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Recharge\Exceptions\RechargeException;
use Recharge\Exceptions\RechargeApiException;
use Recharge\Exceptions\RechargeAuthenticationException;

/**
 * Main client for Recharge API
 *
 * This class provides a fluent interface to interact with the Recharge Payments API.
 * It supports both API versions 2021-01 and 2021-11.
 *
 * @package Recharge
 * @see https://developer.rechargepayments.com/2021-11/
 */
class Client
{
    /**
     * Recharge API base URL
     */
    const API_BASE_URL = 'https://api.rechargeapps.com';

    /**
     * API version 2021-01
     */
    const API_VERSION_2021_01 = '2021-01';

    /**
     * API version 2021-11
     */
    const API_VERSION_2021_11 = '2021-11';

    /**
     * @var GuzzleClient HTTP client instance
     */
    private GuzzleClient $httpClient;

    /**
     * @var string Recharge API access token
     */
    private string $accessToken;

    /**
     * @var string Current API version being used
     */
    private string $apiVersion;

    /**
     * @var array<string, Resources\AbstractResource> Cached resource instances
     */
    private array $resources = [];

    /**
     * Client constructor
     *
     * @param string $accessToken Your Recharge API access token
     * @param string $apiVersion API version to use (default: 2021-11)
     * @throws RechargeException If the API version is invalid
     */
    public function __construct(string $accessToken, string $apiVersion = self::API_VERSION_2021_11)
    {
        $this->accessToken = $accessToken;
        $this->apiVersion = $apiVersion;
        $this->setApiVersion($apiVersion);
    }

    /**
     * Set the API version
     *
     * @param string $apiVersion API version (must be 2021-01 or 2021-11)
     * @return self Returns instance for method chaining
     * @throws RechargeException If the API version is not supported
     */
    public function setApiVersion(string $apiVersion): self
    {
        if (!in_array($apiVersion, [self::API_VERSION_2021_01, self::API_VERSION_2021_11], true)) {
            throw new RechargeException(
                "Unsupported API version: {$apiVersion}. Supported versions: 2021-01, 2021-11"
            );
        }

        $this->apiVersion = $apiVersion;
        $this->initializeHttpClient();

        return $this;
    }

    /**
     * Get the current API version
     *
     * @return string Current API version
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
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
        return $this->request('GET', $endpoint, ['query' => $query]);
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
        return $this->request('POST', $endpoint, ['json' => $data]);
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
        return $this->request('PUT', $endpoint, ['json' => $data]);
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
        return $this->request('DELETE', $endpoint);
    }

    /**
     * Make an HTTP request to the API
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $options Request options
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
            $body = $response->getBody()->getContents();
            return json_decode($body, true) ?? [];
        } catch (RequestException $e) {
            $this->handleException($e);
        } catch (GuzzleException $e) {
            throw new RechargeException("HTTP request failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Initialize the HTTP client with proper headers
     *
     * @return void
     */
    private function initializeHttpClient(): void
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => self::API_BASE_URL,
            'headers' => [
                'X-Recharge-Access-Token' => $this->accessToken,
                'X-Recharge-Version' => $this->apiVersion,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Handle HTTP exceptions and convert them to Recharge exceptions
     *
     * @param RequestException $e The HTTP exception
     * @throws RechargeException Always throws a Recharge exception
     */
    private function handleException(RequestException $e): void
    {
        $response = $e->getResponse();

        if (!$response) {
            throw new RechargeException("Network error: " . $e->getMessage(), 0, $e);
        }

        $statusCode = $response->getStatusCode();
        $bodyContent = $response->getBody()->getContents();
        $body = json_decode($bodyContent, true) ?? [];
        $message = $body['error'] ?? $body['errors'] ?? $response->getReasonPhrase();

        if ($statusCode === 401 || $statusCode === 403) {
            throw new RechargeAuthenticationException($message, $statusCode, $body, $e);
        }

        throw new RechargeApiException($message, $statusCode, $body, $e);
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
