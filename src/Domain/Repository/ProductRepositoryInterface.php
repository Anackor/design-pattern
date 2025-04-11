<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function save(Product $user): void;
}
