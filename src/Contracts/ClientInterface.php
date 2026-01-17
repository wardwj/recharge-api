<?php

declare(strict_types=1);

namespace Recharge\Contracts;

/**
 * Client Interface
 *
 * Defines the contract for the main Recharge API client.
 */
interface ClientInterface
{
    /**
     * Make a GET request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $query Query parameters
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException If the request fails
     */
    public function get(string $endpoint, array $query = []): array;

    /**
     * Make a POST request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException If the request fails
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Make a PUT request to the API
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException If the request fails
     */
    public function put(string $endpoint, array $data = []): array;

    /**
     * Make a DELETE request to the API
     *
     * @param string $endpoint API endpoint path
     * @return array<string, mixed> Response data
     * @throws \Recharge\Exceptions\RechargeException If the request fails
     */
    public function delete(string $endpoint): array;

    /**
     * Get the current API version
     */
    public function getApiVersion(): \Recharge\Enums\ApiVersion;
}
