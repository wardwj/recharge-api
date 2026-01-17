<?php

declare(strict_types=1);

namespace Recharge\Http;

/**
 * HTTP Response wrapper
 *
 * Wraps both body and headers for version-aware pagination handling
 */
final readonly class Response
{
    /**
     * @param array<string, mixed> $body Response body data
     * @param array<string, array<int, string>> $headers Response headers
     */
    public function __construct(
        public array $body,
        public array $headers = []
    ) {
    }

    /**
     * Get a header value
     *
     * @param string $name Header name
     */
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);

        foreach ($this->headers as $key => $values) {
            if (strtolower($key) === $name) {
                return $values[0] ?? null;
            }
        }

        return null;
    }

    /**
     * Extract cursor from Link header (2021-01 format)
     *
     * @return array{next: string|null, previous: string|null}
     */
    public function extractCursorsFromLinkHeader(): array
    {
        $link = $this->getHeader('Link');

        if (!$link) {
            return ['next' => null, 'previous' => null];
        }

        $cursors = ['next' => null, 'previous' => null];

        // Parse Link header: <url>; rel="next", <url>; rel="previous"
        $links = explode(',', $link);

        foreach ($links as $linkPart) {
            if (preg_match('/<([^>]+)>;\s*rel="(next|previous)"/', trim($linkPart), $matches)) {
                $url = $matches[1];
                $rel = $matches[2];

                // Extract cursor from URL query params
                if (preg_match('/[?&]cursor=([^&]+)/', $url, $cursorMatch)) {
                    $cursors[$rel] = urldecode($cursorMatch[1]);
                }
            }
        }

        return $cursors;
    }

    /**
     * Extract cursor from response body (2021-11 format)
     *
     * @return array{next: string|null, previous: string|null}
     */
    public function extractCursorsFromBody(): array
    {
        return [
            'next' => $this->body['next_cursor'] ?? null,
            'previous' => $this->body['previous_cursor'] ?? null,
        ];
    }
}
