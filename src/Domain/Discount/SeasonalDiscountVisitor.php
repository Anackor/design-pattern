<?php

namespace App\Domain\Discount;

use App\Domain\Cart\ProductInterface;
use App\Shared\ValueObject\Money;

class SeasonalDiscountVisitor implements DiscountVisitorInterface
{
    public function visit(ProductInterface $product): Money
    {
        $price = $product->getPrice();

        return $price->multiply(0.85);
    }
}
