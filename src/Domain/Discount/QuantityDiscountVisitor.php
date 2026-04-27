<?php

namespace App\Domain\Discount;

use App\Domain\Cart\ProductInterface;
use App\Shared\ValueObject\Money;

class QuantityDiscountVisitor implements DiscountVisitorInterface
{
    private int $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function visit(ProductInterface $product): Money
    {
        $price = $product->getPrice();

        return $price->multiply(1 - $this->quantity / 100);
    }
}
