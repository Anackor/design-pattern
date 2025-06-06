<?php

namespace App\Domain\Sort;

class SortService {
    private SortStrategyInterface $sortStrategy;

    public function __construct(SortStrategyInterface $sortStrategy) {
        $this->sortStrategy = $sortStrategy;
    }

    public function setSortStrategy(SortStrategyInterface $sortStrategy) {
        $this->sortStrategy = $sortStrategy;
    }

    public function executeSort(array $items): array {
        return $this->sortStrategy->sort($items);
    }
}
