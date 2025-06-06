<?php

namespace App\Domain\Product;

use App\Domain\Discount\DiscountVisitorInterface;

class Product
{
    private string $id;
    private float $price;

    public function __construct(string $id, float $price)
    {
        $this->id = $id;
        $this->price = $price;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function accept(DiscountVisitorInterface $visitor): void
    {
        $visitor->visit($this);
    }
}
