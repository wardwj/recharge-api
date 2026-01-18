<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Contracts\ResourceInterface;
use Recharge\RechargeClient;

/**
 * Abstract base class for all Recharge API resources
 *
 * Provides common functionality and enforces interface contract.
 * All resource classes should extend this base class.
 */
abstract class AbstractResource implements ResourceInterface
{
    protected string $endpoint;

    public function __construct(protected RechargeClient $client)
    {
    }

    /**
     * Get the resource endpoint base path
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Build the full endpoint URL
     *
     * @param string|null $path Additional path to append
     * @return string Full endpoint URL
     */
    protected function buildEndpoint(?string $path = null): string
    {
        if ($path === null) {
            return $this->endpoint;
        }

        return rtrim($this->endpoint, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Get the API client
     */
    protected function getClient(): RechargeClient
    {
        return $this->client;
    }

    /**
     * Build query parameters with defaults
     *
     * @param array<string, mixed> $params User-provided parameters
     * @param array<string, mixed> $defaults Default parameters
     * @return array<string, mixed>
     */
    protected function buildQueryParams(array $params, array $defaults = []): array
    {
        return array_merge($defaults, array_filter($params, fn ($value): bool => $value !== null));
    }

    /**
     * Normalize resource ID
     *
     * @param int|string $id Resource ID
     */
    protected function normalizeId(int|string $id): int
    {
        return (int) $id;
    }
}
