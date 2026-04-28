<?php

namespace App\Domain\Discount;

use App\Domain\Cart\ProductInterface;

/**
 * Applies discount visitors to cart items without conflating this example with
 * the catalog product entity used by persistence and cloning flows.
 */
class DiscountCart
{
    /** @var ProductInterface[] */
    private array $products = [];

    public function addProduct(ProductInterface $product): void
    {
        $this->products[] = $product;
    }

    public function applyDiscount(DiscountVisitorInterface $visitor): void
    {
        foreach ($this->products as $product) {
            $discountedPrice = $visitor->visit($product);
            echo 'Original price: ' . $product->getPrice()->toDisplayString() .
                ' | Discounted price: ' . $discountedPrice->toDisplayString() . "\n";
        }
    }
}
