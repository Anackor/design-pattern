<?php

namespace App\Domain\Cart;

/**
 * SingleProduct is the "Leaf" in the Composite pattern.
 * It represents individual items in the shopping cart.
 */
class SingleProduct implements ProductInterface
{
    public function __construct(
        private string $name,
        private float $price
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
