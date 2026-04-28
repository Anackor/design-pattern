<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    public function catalogCategoryOfId(int $categoryId): ?Category;

    public function addToCatalog(Category $category): void;
}
