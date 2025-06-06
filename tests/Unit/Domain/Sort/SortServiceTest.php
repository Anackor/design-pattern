<?php

namespace App\Tests\Unit\Domain\Sort;

use App\Domain\Sort\Sorter\DateSortStrategy;
use App\Domain\Sort\Sorter\NameSortStrategy;
use App\Domain\Sort\Sorter\PriceSortStrategy;
use App\Domain\Sort\SortService;
use PHPUnit\Framework\TestCase;

class SortServiceTest extends TestCase
{
    public function testSortServiceUsesPriceSortStrategy()
    {
        $priceSortStrategy = new PriceSortStrategy();

        $sortService = new SortService($priceSortStrategy);

        $items = [
            ['name' => 'Item A', 'price' => 10],
            ['name' => 'Item B', 'price' => 5],
            ['name' => 'Item C', 'price' => 20],
        ];

        $sortedItems = $sortService->executeSort($items);

        $this->assertEquals(5, $sortedItems[0]['price']);
        $this->assertEquals(10, $sortedItems[1]['price']);
        $this->assertEquals(20, $sortedItems[2]['price']);
    }

    public function testSortServiceUsesDateSortStrategy()
    {
        $dateSortStrategy = new DateSortStrategy();

        $sortService = new SortService($dateSortStrategy);

        $items = [
            ['name' => 'Item A', 'date' => '2025-01-01'],
            ['name' => 'Item B', 'date' => '2024-01-01'],
            ['name' => 'Item C', 'date' => '2023-01-01'],
        ];

        $sortedItems = $sortService->executeSort($items);

        $this->assertEquals('2023-01-01', $sortedItems[0]['date']);
        $this->assertEquals('2024-01-01', $sortedItems[1]['date']);
        $this->assertEquals('2025-01-01', $sortedItems[2]['date']);
    }

    public function testSortServiceCanChangeStrategy()
    {
        $priceSortStrategy = new PriceSortStrategy();
        $nameSortStrategy = new NameSortStrategy();

        $sortService = new SortService($priceSortStrategy);

        $items = [
            ['name' => 'Item A', 'price' => 10],
            ['name' => 'Item B', 'price' => 5],
            ['name' => 'Item C', 'price' => 20],
        ];

        $sortedItems = $sortService->executeSort($items);
        $this->assertEquals(5, $sortedItems[0]['price']);

        $sortService->setSortStrategy($nameSortStrategy);

        $items = [
            ['name' => 'Charlie', 'price' => 30],
            ['name' => 'Alice', 'price' => 10],
            ['name' => 'Bob', 'price' => 20],
        ];

        $sortedItems = $sortService->executeSort($items);
        $this->assertEquals('Alice', $sortedItems[0]['name']);
    }

}
