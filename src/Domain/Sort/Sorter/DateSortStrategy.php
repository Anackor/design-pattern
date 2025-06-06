<?php

namespace App\Domain\Sort\Sorter;

use App\Domain\Sort\SortStrategyInterface;

class DateSortStrategy implements SortStrategyInterface {
    public function sort(array $items): array {
        usort($items, fn($a, $b) => $a['date'] <=> $b['date']);
        return $items;
    }
}
