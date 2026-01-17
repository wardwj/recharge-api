<?php

declare(strict_types=1);

namespace Recharge\Http;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Recharge\Config\RechargeConfig;
use Recharge\Exceptions\RechargeApiException;
use Recharge\Exceptions\RechargeAuthenticationException;
use Recharge\Exceptions\RechargeException;
use Recharge\Exceptions\RechargeRequestException;

/**
 * HTTP Connector
 *
 * Internal HTTP wrapper that handles PSR-18 client, middleware, and header injection.
 * Automatically injects required Recharge API headers.
 */
final readonly class Connector
{
    /**
     * @param HttpClientInterface $httpClient PSR-18 HTTP client
     * @param RequestFactoryInterface $requestFactory PSR-17 request factory
     * @param StreamFactoryInterface $streamFactory PSR-17 stream factory
     * @param RechargeConfig $config Recharge API configuration
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private RechargeConfig $config
    ) {
    }

    /**
     * Send a GET request
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $query Query parameters
     * @param bool $includeHeaders Whether to return headers (for pagination)
     * @return array<string, mixed>|Response Response data or Response object
     * @throws RechargeException If the request fails
     */
    public function get(string $endpoint, array $query = [], bool $includeHeaders = false): array|Response
    {
        $uri = $this->buildUri($endpoint, $query);
        $request = $this->requestFactory->createRequest('GET', $uri);
        $request = $this->injectHeaders($request);

        return $this->sendRequest($request, $includeHeaders);
    }

    /**
     * Send a POST request
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function post(string $endpoint, array $data = []): array
    {
        $uri = $this->buildUri($endpoint);
        $request = $this->requestFactory->createRequest('POST', $uri);
        $request = $this->injectHeaders($request);
        $request = $this->injectBody($request, $data);

        $response = $this->sendRequest($request);

        return is_array($response) ? $response : $response->body;
    }

    /**
     * Send a PUT request
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $data Request body data
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function put(string $endpoint, array $data = []): array
    {
        $uri = $this->buildUri($endpoint);
        $request = $this->requestFactory->createRequest('PUT', $uri);
        $request = $this->injectHeaders($request);
        $request = $this->injectBody($request, $data);

        $response = $this->sendRequest($request);

        return is_array($response) ? $response : $response->body;
    }

    /**
     * Send a DELETE request
     *
     * @param string $endpoint API endpoint path
     * @return array<string, mixed> Response data
     * @throws RechargeException If the request fails
     */
    public function delete(string $endpoint): array
    {
        $uri = $this->buildUri($endpoint);
        $request = $this->requestFactory->createRequest('DELETE', $uri);
        $request = $this->injectHeaders($request);

        $response = $this->sendRequest($request);

        return is_array($response) ? $response : $response->body;
    }

    /**
     * Build the full URI for the request
     *
     * @param string $endpoint API endpoint path
     * @param array<string, mixed> $query Query parameters
     * @return string Full URI
     */
    private function buildUri(string $endpoint, array $query = []): string
    {
        $baseUrl = rtrim($this->config->getBaseUrl(), '/');
        $endpoint = ltrim($endpoint, '/');
        $uri = $baseUrl . '/' . $endpoint;

        if ($query !== []) {
            $uri .= '?' . http_build_query($query);
        }

        return $uri;
    }

    /**
     * Inject required Recharge API headers
     *
     * @param RequestInterface $request PSR-7 request
     * @return RequestInterface Request with headers injected
     */
    private function injectHeaders(RequestInterface $request): RequestInterface
    {
        return $request
            ->withHeader('X-Recharge-Access-Token', $this->config->getAccessToken())
            ->withHeader('X-Recharge-Version', $this->config->getApiVersionString())
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');
    }

    /**
     * Inject JSON body into the request
     *
     * @param RequestInterface $request PSR-7 request
     * @param array<string, mixed> $data Request body data
     * @return RequestInterface Request with body injected
     */
    private function injectBody(RequestInterface $request, array $data): RequestInterface
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR);
        $stream = $this->streamFactory->createStream($json);

        return $request->withBody($stream);
    }

    /**
     * Send the HTTP request and handle the response
     *
     * @param RequestInterface $request PSR-7 request
     * @param bool $includeHeaders Whether to return headers
     * @return array<string, mixed>|Response Response data or Response object
     * @throws RechargeException If the request fails
     */
    private function sendRequest(RequestInterface $request, bool $includeHeaders = false): array|Response
    {
        try {
            $response = $this->httpClient->sendRequest($request);

            return $this->handleResponse($response, $includeHeaders);
        } catch (\Psr\Http\Client\ClientExceptionInterface $e) {
            throw new RechargeRequestException(
                'HTTP request failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Handle the HTTP response
     *
     * @param ResponseInterface $response PSR-7 response
     * @param bool $includeHeaders Whether to return headers
     * @return array<string, mixed>|Response Response data or Response object
     * @throws RechargeException If the response indicates an error
     */
    private function handleResponse(ResponseInterface $response, bool $includeHeaders = false): array|Response
    {
        $statusCode = $response->getStatusCode();
        $bodyContent = $response->getBody()->getContents();
        $body = json_decode($bodyContent, true) ?? [];

        // Handle error responses
        if ($statusCode >= 400) {
            // Extract error message (can be string or array)
            $message = $response->getReasonPhrase();
            if (isset($body['error'])) {
                $message = is_string($body['error']) ? $body['error'] : (string) json_encode($body['error']);
            } elseif (isset($body['errors'])) {
                $message = is_string($body['errors']) ? $body['errors'] : (string) json_encode($body['errors']);
            }

            if ($statusCode === 401 || $statusCode === 403) {
                throw new RechargeAuthenticationException(
                    $message,
                    $statusCode,
                    $body
                );
            }

            throw new RechargeApiException(
                $message,
                $statusCode,
                $body
            );
        }

        if ($includeHeaders) {
            return new Response($body, $response->getHeaders());
        }

        return $body;
    }
}
