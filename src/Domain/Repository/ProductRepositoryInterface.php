<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function catalogProductOfId(int $productId): ?Product;

    public function addToCatalog(Product $product): void;
}
