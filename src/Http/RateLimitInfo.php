<?php

declare(strict_types=1);

namespace Recharge\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Rate Limit Information
 *
 * Tracks API rate limiting metadata from response headers.
 * Helps applications implement intelligent backoff strategies.
 */
final readonly class RateLimitInfo
{
    /**
     * @param int|null $limit Maximum requests allowed
     * @param int|null $remaining Remaining requests in window
     * @param int|null $reset Timestamp when limit resets
     * @param int|null $retryAfter Seconds to wait before retry (for 429 responses)
     */
    public function __construct(
        public ?int $limit = null,
        public ?int $remaining = null,
        public ?int $reset = null,
        public ?int $retryAfter = null
    ) {
    }

    /**
     * Extract rate limit info from response headers
     *
     * @param ResponseInterface $response PSR-7 response
     */
    public static function fromResponse(ResponseInterface $response): self
    {
        return new self(
            limit: self::getHeaderValue($response, 'X-RateLimit-Limit'),
            remaining: self::getHeaderValue($response, 'X-RateLimit-Remaining'),
            reset: self::getHeaderValue($response, 'X-RateLimit-Reset'),
            retryAfter: self::getHeaderValue($response, 'Retry-After')
        );
    }

    /**
     * Check if rate limit is exhausted
     */
    public function isExhausted(): bool
    {
        return $this->remaining !== null && $this->remaining <= 0;
    }

    /**
     * Check if approaching rate limit (< 10% remaining)
     */
    public function isApproachingLimit(): bool
    {
        if ($this->limit === null || $this->remaining === null) {
            return false;
        }

        $threshold = (int) floor($this->limit * 0.1);

        return $this->remaining <= $threshold;
    }

    /**
     * Get seconds until rate limit resets
     *
     * @return int|null Seconds until reset, or null if unknown
     */
    public function getSecondsUntilReset(): ?int
    {
        if ($this->reset === null) {
            return null;
        }

        $diff = $this->reset - time();

        return max(0, $diff);
    }

    /**
     * Convert to array for logging
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'remaining' => $this->remaining,
            'reset' => $this->reset,
            'reset_in_seconds' => $this->getSecondsUntilReset(),
            'retry_after' => $this->retryAfter,
            'is_exhausted' => $this->isExhausted(),
            'is_approaching_limit' => $this->isApproachingLimit(),
        ];
    }

    /**
     * Extract integer value from response header
     *
     * @param ResponseInterface $response PSR-7 response
     * @param string $headerName Header name
     */
    private static function getHeaderValue(ResponseInterface $response, string $headerName): ?int
    {
        if (!$response->hasHeader($headerName)) {
            return null;
        }

        $value = $response->getHeaderLine($headerName);

        return is_numeric($value) ? (int) $value : null;
    }
}
