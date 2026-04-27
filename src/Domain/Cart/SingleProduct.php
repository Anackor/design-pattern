<?php

namespace App\Domain\Cart;

use App\Shared\ValueObject\Money;

/**
 * SingleProduct is the "Leaf" in the Composite pattern.
 * It represents individual items in the shopping cart.
 */
class SingleProduct implements ProductInterface
{
    private Money $price;

    public function __construct(
        private string $name,
        float $price
    ) {
        $this->price = Money::fromFloat($price);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }
}
