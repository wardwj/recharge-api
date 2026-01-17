<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Support\Collection;

/**
 * Unit tests for Collection class
 */
class CollectionTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testData = [
            (object) ['id' => 1, 'name' => 'Item 1', 'price' => 10.00],
            (object) ['id' => 2, 'name' => 'Item 2', 'price' => 20.00],
            (object) ['id' => 3, 'name' => 'Item 3', 'price' => 30.00],
            (object) ['id' => 4, 'name' => 'Item 4', 'price' => 40.00],
            (object) ['id' => 5, 'name' => 'Item 5', 'price' => 50.00],
        ];
    }

    public function testCollectionCreation(): void
    {
        $collection = new Collection($this->testData);

        $this->assertCount(5, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testCollectionAll(): void
    {
        $collection = new Collection($this->testData);

        $this->assertEquals($this->testData, $collection->all());
    }

    public function testCollectionFirst(): void
    {
        $collection = new Collection($this->testData);

        $first = $collection->first();
        $this->assertEquals(1, $first->id);
    }

    public function testCollectionFirstOnEmpty(): void
    {
        $collection = new Collection([]);

        $this->assertNull($collection->first());
    }

    public function testCollectionLast(): void
    {
        $collection = new Collection($this->testData);

        $last = $collection->last();
        $this->assertEquals(5, $last->id);
    }

    public function testCollectionIsEmpty(): void
    {
        $empty = new Collection([]);
        $notEmpty = new Collection($this->testData);

        $this->assertTrue($empty->isEmpty());
        $this->assertFalse($notEmpty->isEmpty());
    }

    public function testCollectionIsNotEmpty(): void
    {
        $empty = new Collection([]);
        $notEmpty = new Collection($this->testData);

        $this->assertFalse($empty->isNotEmpty());
        $this->assertTrue($notEmpty->isNotEmpty());
    }

    public function testCollectionMap(): void
    {
        $collection = new Collection($this->testData);

        $names = $collection->map(fn ($item) => $item->name);

        $this->assertEquals(['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'], $names->all());
    }

    public function testCollectionFilter(): void
    {
        $collection = new Collection($this->testData);

        $expensive = $collection->filter(fn ($item): bool => $item->price > 25.00);

        $this->assertCount(3, $expensive);
        $this->assertEquals(3, $expensive->first()->id);
    }

    public function testCollectionFind(): void
    {
        $collection = new Collection($this->testData);

        $found = $collection->find(fn ($item): bool => $item->id === 3);

        $this->assertNotNull($found);
        $this->assertEquals('Item 3', $found->name);
    }

    public function testCollectionFindNotFound(): void
    {
        $collection = new Collection($this->testData);

        $found = $collection->find(fn ($item): bool => $item->id === 999);

        $this->assertNull($found);
    }

    public function testCollectionContains(): void
    {
        $collection = new Collection($this->testData);

        $this->assertTrue($collection->contains(fn ($item): bool => $item->id === 3));
        $this->assertFalse($collection->contains(fn ($item): bool => $item->id === 999));
    }

    public function testCollectionChunk(): void
    {
        $collection = new Collection($this->testData);

        $chunks = $collection->chunk(2);

        $this->assertCount(3, $chunks);
        $this->assertCount(2, $chunks->first());
        $this->assertCount(1, $chunks->last());
    }

    public function testCollectionTake(): void
    {
        $collection = new Collection($this->testData);

        $taken = $collection->take(3);

        $this->assertCount(3, $taken);
        $this->assertEquals(1, $taken->first()->id);
        $this->assertEquals(3, $taken->last()->id);
    }

    public function testCollectionSkip(): void
    {
        $collection = new Collection($this->testData);

        $skipped = $collection->skip(2);

        $this->assertCount(3, $skipped);
        $this->assertEquals(3, $skipped->first()->id);
    }

    public function testCollectionReduce(): void
    {
        $collection = new Collection($this->testData);

        $total = $collection->reduce(fn ($sum, $item): float|int|array => $sum + $item->price, 0.0);

        $this->assertEquals(150.00, $total);
    }

    public function testCollectionIteration(): void
    {
        $collection = new Collection($this->testData);

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->id;
        }

        $this->assertEquals([1, 2, 3, 4, 5], $ids);
    }

    public function testCollectionCount(): void
    {
        $collection = new Collection($this->testData);

        $this->assertCount(5, $collection);
        $this->assertEquals(5, $collection->count());
    }

    public function testCollectionArrayAccess(): void
    {
        $collection = new Collection($this->testData);

        $this->assertTrue(isset($collection[0]));
        $this->assertEquals(1, $collection[0]->id);
        $this->assertFalse(isset($collection[999]));
    }

    public function testCollectionIsImmutable(): void
    {
        $collection = new Collection($this->testData);

        $this->expectException(\BadMethodCallException::class);
        $collection[0] = 'new value';
    }

    public function testCollectionToJson(): void
    {
        $collection = new Collection([
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test 2'],
        ]);

        $json = $collection->toJson();

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertCount(2, $decoded);
    }
}
