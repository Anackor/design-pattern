<?php

namespace App\Application\Cart;

use App\Domain\Cart\ProductInterface;

/**
 * Class Cart
 *
 * This class represents a shopping cart that can contain both individual products
 * and groups of products (bundles), following the Composite design pattern.
 *
 * Composite Pattern Explanation:
 * The Composite pattern allows treating individual objects and compositions of objects uniformly.
 * In this case, both SingleProduct and ProductBundle implement the ProductInterface,
 * so the Cart doesn't need to know whether it’s dealing with a simple product or a group.
 *
 * Benefits in this context:
 * - Simplifies logic: Cart only works with ProductInterface, so it doesn't need to differentiate
 *   between single products or nested bundles.
 * - High flexibility: New product types (e.g. discount bundles, digital downloads) can be added
 *   without changing the Cart logic.
 * - Extensible hierarchy: ProductBundles can contain other ProductBundles, allowing for deeply
 *   nested product structures (like a laptop bundle that contains accessories bundles).
 * - Reduces duplication: All product elements define their own price, and the Cart aggregates it
 *   recursively without custom code per type.
 *
 * This makes the Cart class open to extension (new product types) but closed to modification,
 * following the Open/Closed Principle (SOLID).
 */
class Cart
{
    /** @var ProductInterface[] */
    private array $items = [];

    /**
     * Adds a product or bundle to the cart.
     */
    public function addItem(ProductInterface $product): void
    {
        $this->items[] = $product;
    }

    /**
     * Returns the total price of all items, recursively handling bundles.
     */
    public function getTotal(): float
    {
        return array_reduce(
            $this->items,
            fn (float $total, ProductInterface $item) => $total + $item->getPrice(),
            0.0
        );
    }

    /**
     * Returns a list of item names (flattened).
     */
    public function getItemNames(): array
    {
        return array_map(fn (ProductInterface $item) => $item->getName(), $this->items);
    }
}
