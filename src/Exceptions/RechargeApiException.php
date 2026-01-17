<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

/**
 * Exception thrown when the Recharge API returns an error response (4xx, 5xx)
 *
 * This includes API-level errors such as:
 * - 400 Bad Request (validation errors from API)
 * - 404 Not Found (resource not found)
 * - 422 Unprocessable Entity
 * - 500 Internal Server Error
 *
 * Does NOT include authentication errors (401/403) - use RechargeAuthenticationException
 */
class RechargeApiException extends RechargeException
{
    /**
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param array<string, mixed>|null $responseBody Response body from API
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        int $code = 0,
        private ?array $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the full response body from the API
     *
     * Useful for debugging or extracting additional error details
     *
     * @return array<string, mixed>|null
     */
    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }

    /**
     * Get a specific field from the response body
     *
     * @param string $key Field key
     * @return mixed|null
     */
    public function getResponseField(string $key): mixed
    {
        return $this->responseBody[$key] ?? null;
    }
}
