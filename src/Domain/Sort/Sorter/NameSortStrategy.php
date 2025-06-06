<?php

namespace App\Domain\Sort\Sorter;

use App\Domain\Sort\SortStrategyInterface;

class NameSortStrategy implements SortStrategyInterface {
    public function sort(array $items): array {
        usort($items, fn($a, $b) => strcmp($a['name'], $b['name']));
        return $items;
    }
}
