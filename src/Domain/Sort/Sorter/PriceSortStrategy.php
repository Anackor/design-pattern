<?php

namespace App\Domain\Sort\Sorter;

use App\Domain\Sort\SortStrategyInterface;

class PriceSortStrategy implements SortStrategyInterface {
    public function sort(array $items): array {
        usort($items, fn($a, $b) => $a['price'] <=> $b['price']);
        return $items;
    }
}
