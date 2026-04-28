<?php

namespace App\Domain\Discount;

use App\Domain\Cart\ProductInterface;
use App\Shared\ValueObject\Money;

interface DiscountVisitorInterface
{
    public function visit(ProductInterface $product): Money;
}
