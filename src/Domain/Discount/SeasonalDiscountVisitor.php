<?php

namespace App\Domain\Discount;

use App\Domain\Product\Product;

class SeasonalDiscountVisitor implements DiscountVisitorInterface
{
    public function visit(Product $product): float
    {
        $price = $product->getPrice();

        $price *= 0.85;

        return $price;
    }
}
