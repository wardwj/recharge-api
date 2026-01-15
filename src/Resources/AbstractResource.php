<?php

namespace Recharge\Resources;

use Recharge\Client;

/**
 * Abstract base class for all Recharge API resources
 *
 * @package Recharge\Resources
 */
abstract class AbstractResource
{
    /**
     * @var Client The Recharge API client
     */
    protected Client $client;

    /**
     * @var string The resource endpoint base path
     */
    protected string $endpoint;

    /**
     * AbstractResource constructor
     *
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
}
