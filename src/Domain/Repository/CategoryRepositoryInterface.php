<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function save(Category $user): void;
}
