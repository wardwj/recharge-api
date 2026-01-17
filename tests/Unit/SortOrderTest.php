<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Recharge\Support\SortOrder;

class SortOrderTest extends TestCase
{
    public function testValidateAcceptsValidSortBy(): void
    {
        SortOrder::validate('id-desc', SortOrder::SUBSCRIPTIONS);
        SortOrder::validate('created_at-asc', SortOrder::SUBSCRIPTIONS);
        SortOrder::validate('updated_at-desc', SortOrder::SUBSCRIPTIONS);

        $this->assertTrue(true); // If we get here, validation passed
    }

    public function testValidateAcceptsNull(): void
    {
        SortOrder::validate(null, SortOrder::SUBSCRIPTIONS);

        $this->assertTrue(true); // If we get here, validation passed
    }

    public function testValidateThrowsExceptionForInvalidSortBy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sort_by value "invalid-sort"');

        SortOrder::validate('invalid-sort', SortOrder::SUBSCRIPTIONS);
    }

    public function testValidateIncludesAllowedValuesInErrorMessage(): void
    {
        try {
            SortOrder::validate('bad-value', SortOrder::SUBSCRIPTIONS);
            $this->fail('Expected InvalidArgumentException to be thrown');
        } catch (InvalidArgumentException $e) {
            $message = $e->getMessage();
            $this->assertStringContainsString('Invalid sort_by value', $message);
            $this->assertStringContainsString('bad-value', $message);
            $this->assertStringContainsString('id-asc', $message);
            $this->assertStringContainsString('id-desc', $message);
        }
    }

    public function testGetDefaultReturnsIdDesc(): void
    {
        $default = SortOrder::getDefault(SortOrder::SUBSCRIPTIONS);

        $this->assertEquals('id-desc', $default);
    }

    public function testGetDefaultFallsBackToFirstValue(): void
    {
        $customValues = ['custom-asc', 'custom-desc'];
        $default = SortOrder::getDefault($customValues);

        $this->assertEquals('custom-asc', $default);
    }

    public function testChargesSortOrderIncludesScheduledAt(): void
    {
        $this->assertContains('scheduled_at-asc', SortOrder::CHARGES);
        $this->assertContains('scheduled_at-desc', SortOrder::CHARGES);
    }

    public function testOrdersSortOrderIncludesScheduledAt(): void
    {
        $this->assertContains('scheduled_at-asc', SortOrder::ORDERS);
        $this->assertContains('scheduled_at-desc', SortOrder::ORDERS);
    }

    public function testSubscriptionsSortOrderDoesNotIncludeScheduledAt(): void
    {
        $this->assertNotContains('scheduled_at-asc', SortOrder::SUBSCRIPTIONS);
        $this->assertNotContains('scheduled_at-desc', SortOrder::SUBSCRIPTIONS);
    }
}
