<?php

namespace App\Domain\Product;

use App\Domain\Product\Product;
use App\Domain\Discount\DiscountVisitorInterface;

/**
 * The ShoppingCart class represents a collection of products that a customer intends to purchase.
 * 
 * This class applies the Visitor pattern to allow external logic (i.e., discount visitors) to be applied 
 * to the products within the cart without modifying the products themselves. By utilizing the Visitor pattern,
 * we are able to introduce different types of discounts (e.g., quantity-based, seasonal) without changing the 
 * core product logic. This makes it easy to extend the discount functionality in the future by adding new visitors.
 *
 * Benefits of using the Visitor pattern here:
 * - Extensibility: New discount types can be added by creating new visitor classes, without modifying 
 *   the core ShoppingCart or Product classes.
 * - Separation of Concerns: The logic for applying discounts is separated from the product or order 
 *   logic, adhering to the Single Responsibility Principle.
 * - Flexibility: Multiple discount strategies can be applied to the products in the cart in a clean 
 *   and maintainable way.
 */
class ShoppingCart
{
    private array $products = [];

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function applyDiscount(DiscountVisitorInterface $visitor): void
    {
        foreach ($this->products as $product) {
            $discountedPrice = $visitor->visit($product);
            echo "Original price: " . $product->getPrice() . " | Discounted price: " . $discountedPrice . "\n";
        }
    }
}
