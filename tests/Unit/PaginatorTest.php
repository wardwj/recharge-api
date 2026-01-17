<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Data\Cursor;

/**
 * Tests for Paginator cursor parsing
 *
 * Verifies cursor extraction logic for both API versions:
 * - 2021-01: Cursors in Link HTTP headers
 * - 2021-11: Cursors in JSON response body
 */
class PaginatorTest extends TestCase
{
    // Cursor Tests - Version-Aware Parsing

    public function testCursorParsesFromJsonBody(): void
    {
        $data = [
            'next_cursor' => 'cursor_abc123',
            'previous_cursor' => 'cursor_xyz789',
        ];

        $cursor = Cursor::fromArray($data);

        $this->assertEquals('cursor_abc123', $cursor->next);
        $this->assertEquals('cursor_xyz789', $cursor->previous);
        $this->assertTrue($cursor->hasNext());
        $this->assertTrue($cursor->hasPrevious());
    }

    public function testCursorParsesFromLinkHeader(): void
    {
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?cursor=next_cursor_123>; rel="next", <https://api.rechargeapps.com/subscriptions?cursor=prev_cursor_456>; rel="previous"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertEquals('next_cursor_123', $cursor->next);
        $this->assertEquals('prev_cursor_456', $cursor->previous);
        $this->assertTrue($cursor->hasNext());
        $this->assertTrue($cursor->hasPrevious());
    }

    public function testCursorParsesLinkHeaderWithOnlyNext(): void
    {
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?cursor=next_only>; rel="next"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertEquals('next_only', $cursor->next);
        $this->assertNull($cursor->previous);
        $this->assertTrue($cursor->hasNext());
        $this->assertFalse($cursor->hasPrevious());
    }

    public function testCursorParsesLinkHeaderWithOnlyPrevious(): void
    {
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?cursor=prev_only>; rel="previous"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertNull($cursor->next);
        $this->assertEquals('prev_only', $cursor->previous);
        $this->assertFalse($cursor->hasNext());
        $this->assertTrue($cursor->hasPrevious());
    }

    public function testCursorHandlesEmptyLinkHeader(): void
    {
        $cursor = Cursor::fromLinkHeader(null);

        $this->assertNull($cursor->next);
        $this->assertNull($cursor->previous);
        $this->assertFalse($cursor->hasNext());
        $this->assertFalse($cursor->hasPrevious());
    }

    public function testCursorHandlesEmptyJsonBody(): void
    {
        $cursor = Cursor::fromArray([]);

        $this->assertNull($cursor->next);
        $this->assertNull($cursor->previous);
        $this->assertFalse($cursor->hasNext());
        $this->assertFalse($cursor->hasPrevious());
    }

    public function testCursorHandlesNullCursorsInJsonBody(): void
    {
        $data = [
            'next_cursor' => null,
            'previous_cursor' => null,
        ];

        $cursor = Cursor::fromArray($data);

        $this->assertNull($cursor->next);
        $this->assertNull($cursor->previous);
        $this->assertFalse($cursor->hasNext());
        $this->assertFalse($cursor->hasPrevious());
    }

    public function testCursorParsesComplexLinkHeaderFormat(): void
    {
        // Real-world example with full URLs and additional parameters
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?limit=50&cursor=eyJpZCI6MTIzfQ%3D%3D&status=active>; rel="next"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertEquals('eyJpZCI6MTIzfQ==', $cursor->next); // URL decoded
        $this->assertNull($cursor->previous);
    }

    public function testCursorToArrayIncludesAllFields(): void
    {
        $cursor = new Cursor(
            next: 'next_cursor',
            previous: 'prev_cursor'
        );

        $array = $cursor->toArray();

        $this->assertArrayHasKey('next_cursor', $array);
        $this->assertArrayHasKey('previous_cursor', $array);
        $this->assertEquals('next_cursor', $array['next_cursor']);
        $this->assertEquals('prev_cursor', $array['previous_cursor']);
    }

    public function testCursorToArrayFiltersNullValues(): void
    {
        $cursor = new Cursor(next: 'next_only');

        $array = $cursor->toArray();

        $this->assertArrayHasKey('next_cursor', $array);
        $this->assertArrayNotHasKey('previous_cursor', $array);
    }

    // Edge Cases

    public function testCursorHandlesMalformedLinkHeader(): void
    {
        $linkHeader = 'not-a-valid-link-header';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertNull($cursor->next);
        $this->assertNull($cursor->previous);
    }

    public function testCursorHandlesLinkHeaderWithoutCursorParameter(): void
    {
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?limit=50>; rel="next"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertNull($cursor->next);
        $this->assertNull($cursor->previous);
    }

    public function testCursorHandlesMultipleLinkHeaderFormats(): void
    {
        // Some APIs use comma-separated, others use array of strings
        $linkHeader = '<https://api.rechargeapps.com/subscriptions?cursor=next>; rel="next", <https://api.rechargeapps.com/subscriptions?cursor=prev>; rel="previous"';

        $cursor = Cursor::fromLinkHeader($linkHeader);

        $this->assertEquals('next', $cursor->next);
        $this->assertEquals('prev', $cursor->previous);
    }
}
