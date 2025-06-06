<?php

namespace App\Domain\Discount;

use App\Domain\Product\Product;

class QuantityDiscountVisitor implements DiscountVisitorInterface
{
    private int $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function visit(Product $product): float
    {
        $price = $product->getPrice();

        $price *= (1 - $this->quantity / 100);

        return $price;
    }
}
