<?php

declare(strict_types=1);

namespace Recharge\Config;

use Recharge\Enums\ApiVersion;
use Recharge\Exceptions\ValidationException;

/**
 * Recharge API Configuration
 *
 * Handles API key, version, and base URL configuration.
 * Immutable once constructed.
 */
final readonly class RechargeConfig
{
    /**
     * Default Recharge API base URL
     */
    private const DEFAULT_BASE_URL = 'https://api.rechargeapps.com';

    /**
     * @param string $accessToken Recharge API access token
     * @param ApiVersion $apiVersion API version to use
     * @param string $baseUrl Base URL for the API (default: https://api.rechargeapps.com)
     * @param int $timeout Request timeout in seconds (default: 30)
     */
    public function __construct(
        private string $accessToken,
        private ApiVersion $apiVersion = ApiVersion::V2021_11,
        private string $baseUrl = self::DEFAULT_BASE_URL,
        private int $timeout = 30
    ) {
        if ($this->accessToken === '' || $this->accessToken === '0') {
            throw new ValidationException('API access token cannot be empty');
        }

        if ($this->timeout < 1) {
            throw new ValidationException('Timeout must be at least 1 second');
        }
    }

    /**
     * Get the API access token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Get the API version
     */
    public function getApiVersion(): ApiVersion
    {
        return $this->apiVersion;
    }

    /**
     * Get the API version as string
     */
    public function getApiVersionString(): string
    {
        return $this->apiVersion->value;
    }

    /**
     * Get the base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the request timeout
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Create a new config with updated API version
     *
     * @param ApiVersion $apiVersion New API version
     */
    public function withApiVersion(ApiVersion $apiVersion): self
    {
        return new self(
            $this->accessToken,
            $apiVersion,
            $this->baseUrl,
            $this->timeout
        );
    }
}
