<?php

declare(strict_types=1);

namespace Recharge\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Request Context
 *
 * Captures metadata about API requests for debugging, logging, and error reporting.
 * Provides full transparency into what was sent and received.
 */
final readonly class RequestContext
{
    /**
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $uri Full request URI
     * @param array<string, mixed> $headers Request headers
     * @param string|null $body Request body (JSON)
     * @param int|null $statusCode Response status code
     * @param array<string, mixed> $responseHeaders Response headers
     * @param string|null $responseBody Response body (JSON)
     * @param float $duration Request duration in seconds
     * @param \Throwable|null $exception Exception if request failed
     */
    public function __construct(
        public string $method,
        public string $uri,
        public array $headers = [],
        public ?string $body = null,
        public ?int $statusCode = null,
        public array $responseHeaders = [],
        public ?string $responseBody = null,
        public float $duration = 0.0,
        public ?\Throwable $exception = null
    ) {
    }

    /**
     * Create from PSR-7 request
     *
     * @param RequestInterface $request PSR-7 request
     */
    public static function fromRequest(RequestInterface $request): self
    {
        return new self(
            method: $request->getMethod(),
            uri: (string) $request->getUri(),
            headers: self::sanitizeHeaders($request->getHeaders()),
            body: $request->getBody()->getContents()
        );
    }

    /**
     * Add response data to context
     *
     * @param ResponseInterface $response PSR-7 response
     * @param float $duration Request duration in seconds
     * @return self New instance with response data
     */
    public function withResponse(ResponseInterface $response, float $duration): self
    {
        return new self(
            method: $this->method,
            uri: $this->uri,
            headers: $this->headers,
            body: $this->body,
            statusCode: $response->getStatusCode(),
            responseHeaders: self::sanitizeHeaders($response->getHeaders()),
            responseBody: $response->getBody()->getContents(),
            duration: $duration,
            exception: $this->exception
        );
    }

    /**
     * Add exception to context
     *
     * @param \Throwable $exception Exception that occurred
     * @return self New instance with exception
     */
    public function withException(\Throwable $exception): self
    {
        return new self(
            method: $this->method,
            uri: $this->uri,
            headers: $this->headers,
            body: $this->body,
            statusCode: $this->statusCode,
            responseHeaders: $this->responseHeaders,
            responseBody: $this->responseBody,
            duration: $this->duration,
            exception: $exception
        );
    }

    /**
     * Sanitize headers (remove sensitive data)
     *
     * @param array<string, array<string>> $headers Headers array
     * @return array<string, mixed>
     */
    private static function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];
        $sensitiveHeaders = ['X-Recharge-Access-Token', 'Authorization'];

        foreach ($headers as $name => $values) {
            $sanitized[$name] = in_array($name, $sensitiveHeaders, true) ? '***REDACTED***' : implode(', ', $values);
        }

        return $sanitized;
    }

    /**
     * Convert to array for logging
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'uri' => $this->uri,
            'headers' => $this->headers,
            'body' => $this->body,
            'status_code' => $this->statusCode,
            'response_headers' => $this->responseHeaders,
            'response_body' => $this->responseBody,
            'duration' => round($this->duration, 3),
            'exception' => $this->exception instanceof \Throwable ? [
                'class' => $this->exception::class,
                'message' => $this->exception->getMessage(),
                'code' => $this->exception->getCode(),
            ] : null,
        ];
    }

    /**
     * Get summary for exception messages
     */
    public function getSummary(): string
    {
        $summary = sprintf('%s %s', $this->method, $this->uri);

        if ($this->statusCode !== null) {
            $summary .= sprintf(' [%d]', $this->statusCode);
        }

        if ($this->duration > 0) {
            $summary .= sprintf(' (%.3fs)', $this->duration);
        }

        return $summary;
    }
}
