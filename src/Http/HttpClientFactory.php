<?php

declare(strict_types=1);

namespace Recharge\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * HTTP Client Factory
 *
 * Factory for creating PSR-18 HTTP client and PSR-17 factories.
 * Uses php-http/discovery to auto-discover implementations.
 */
final class HttpClientFactory
{
    /**
     * Create a PSR-18 HTTP client
     */
    public static function createClient(): HttpClientInterface
    {
        return Psr18ClientDiscovery::find();
    }

    /**
     * Create a PSR-17 request factory
     */
    public static function createRequestFactory(): RequestFactoryInterface
    {
        return Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * Create a PSR-17 stream factory
     */
    public static function createStreamFactory(): StreamFactoryInterface
    {
        return Psr17FactoryDiscovery::findStreamFactory();
    }
}
