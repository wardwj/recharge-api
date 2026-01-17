<?php

declare(strict_types=1);

namespace Recharge\Data;

/**
 * Cursor pagination metadata
 *
 * Represents cursor-based pagination information from API responses.
 */
final readonly class Cursor
{
    /**
     * @param string|null $next Next page cursor
     * @param string|null $previous Previous page cursor
     */
    public function __construct(
        public ?string $next = null,
        public ?string $previous = null
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Response data containing cursor information
     */
    public static function fromArray(array $data): static
    {
        return new self(
            next: $data['next_cursor'] ?? $data['next'] ?? null,
            previous: $data['previous_cursor'] ?? $data['previous'] ?? null
        );
    }

    /**
     * Create from Link HTTP header (2021-01 API)
     *
     * Parses cursor values from Link headers like:
     * <https://api.rechargeapps.com/subscriptions?cursor=abc123>; rel="next"
     *
     * @param string|null $linkHeader The Link header value
     */
    public static function fromLinkHeader(?string $linkHeader): static
    {
        $next = null;
        $previous = null;

        if ($linkHeader) {
            // Parse next cursor
            if (preg_match('/<([^>]+)>;\s*rel="next"/', $linkHeader, $nextMatch)) {
                $next = self::extractCursorFromUrl($nextMatch[1]);
            }

            // Parse previous cursor
            if (preg_match('/<([^>]+)>;\s*rel="previous"/', $linkHeader, $previousMatch)) {
                $previous = self::extractCursorFromUrl($previousMatch[1]);
            }
        }

        return new self(next: $next, previous: $previous);
    }

    /**
     * Extract cursor parameter from URL
     *
     * @param string $url Full URL with query parameters
     */
    private static function extractCursorFromUrl(string $url): ?string
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (!is_string($queryString)) {
            return null;
        }

        parse_str($queryString, $params);

        $cursor = $params['cursor'] ?? null;

        return is_string($cursor) ? $cursor : null;
    }

    /**
     * Convert to array representation
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->next !== null) {
            $data['next_cursor'] = $this->next;
        }

        if ($this->previous !== null) {
            $data['previous_cursor'] = $this->previous;
        }

        return $data;
    }

    /**
     * Check if there is a next page
     */
    public function hasNext(): bool
    {
        return $this->next !== null && $this->next !== '';
    }

    /**
     * Check if there is a previous page
     */
    public function hasPrevious(): bool
    {
        return $this->previous !== null && $this->previous !== '';
    }
}
