<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Contracts\ResourceInterface;
use Recharge\Enums\ApiVersion;
use Recharge\RechargeClient;
use Recharge\Support\SortValidator;
use Recharge\Support\VersionContext;

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

    /**
     * Get the sort enum class for this resource
     *
     * Override this method in child classes to return the sort enum class name.
     * Return null if the resource doesn't support sorting.
     *
     * @return class-string<\BackedEnum>|null The sort enum class name, or null if not supported
     */
    protected function getSortEnumClass(): ?string
    {
        return null;
    }

    /**
     * Validate sort_by parameter in query params
     *
     * Accepts a sort enum instance or string, converts enum to string,
     * and validates the string value against the enum class.
     * Auto-determines the enum class from the resource's getSortEnumClass() method.
     *
     * @param array<string, mixed> $queryParams Query parameters (may be modified)
     * @return array<string, mixed> Modified query parameters with validated sort_by
     * @throws \InvalidArgumentException If sort_by value is invalid or resource doesn't support sorting
     */
    protected function validateSort(array $queryParams): array
    {
        $enumClass = $this->getSortEnumClass();

        if ($enumClass === null) {
            // If resource doesn't support sorting but sort_by is provided, throw error
            if (isset($queryParams['sort_by'])) {
                throw new \InvalidArgumentException(
                    sprintf('Resource %s does not support sorting', static::class)
                );
            }

            return $queryParams;
        }

        return SortValidator::normalizeAndValidate($queryParams, $enumClass);
    }

    /**
     * Switch to a specific API version and return a context that restores automatically
     *
     * The version will be automatically restored when the context is destroyed.
     * Use with a try-finally pattern or let the context handle restoration via destructor.
     *
     * @param ApiVersion $requiredVersion The API version required for this operation
     * @return VersionContext Context that will restore the original version on destruction
     */
    protected function switchToVersion(ApiVersion $requiredVersion): VersionContext
    {
        $originalVersion = $this->client->getApiVersion();

        if ($originalVersion !== $requiredVersion) {
            $this->client->setApiVersion($requiredVersion);
        }

        return new VersionContext($this->client, $originalVersion);
    }
}
