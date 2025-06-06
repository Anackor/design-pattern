<?php

namespace App\Domain\Discount;

use App\Domain\Product\Product;

interface DiscountVisitorInterface
{
    public function visit(Product $product): float;
}
