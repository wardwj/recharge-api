<?php

declare(strict_types=1);

namespace Recharge\Exceptions;

use Recharge\Http\RateLimitInfo;

/**
 * Exception thrown when the API rate limit is exceeded (429)
 *
 * This exception is thrown when too many requests have been made
 * within the rate limit window.
 *
 * The exception includes rate limit information via getRateLimitInfo()
 * to help determine when retries are safe.
 *
 * @see https://developer.rechargepayments.com/2021-11/responses
 */
class RechargeRateLimitException extends RechargeApiException
{
    /**
     * @var RateLimitInfo|null
     */
    private ?RateLimitInfo $rateLimitInfo = null;

    /**
     * @param string $message Error message
     * @param int $code HTTP status code (should be 429)
     * @param array<string, mixed>|null $responseBody Response body from API
     * @param RateLimitInfo|null $rateLimitInfo Rate limit information from headers
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        int $code = 429,
        ?array $responseBody = null,
        ?RateLimitInfo $rateLimitInfo = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $responseBody, $previous);
        $this->rateLimitInfo = $rateLimitInfo;
    }

    /**
     * Get rate limit information from response headers
     *
     * Contains information about rate limits and retry-after timing.
     *
     * @return RateLimitInfo|null Rate limit information, or null if not available
     */
    public function getRateLimitInfo(): ?RateLimitInfo
    {
        return $this->rateLimitInfo;
    }

    /**
     * Get seconds to wait before retrying
     *
     * @return int|null Seconds to wait, or null if not available
     */
    public function getRetryAfter(): ?int
    {
        return $this->rateLimitInfo?->retryAfter;
    }
}
