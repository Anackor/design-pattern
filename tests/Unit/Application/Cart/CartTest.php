<?php

namespace Tests\Application\Cart;

use PHPUnit\Framework\TestCase;
use App\Application\Cart\Cart;
use App\Domain\Cart\SingleProduct;
use App\Domain\Cart\ProductBundle;

/**
 * Tests the Cart class using composite products.
 */
class CartTest extends TestCase
{
    public function testCartWithSingleAndBundledProducts(): void
    {
        // Individual products
        $productA = new SingleProduct('Book', 12.99);
        $productB = new SingleProduct('Pen', 1.99);
        $productC = new SingleProduct('Notebook', 5.49);

        // A bundle that includes B and C
        $bundle1 = new ProductBundle('Stationery Set');
        $bundle1->add($productB);
        $bundle1->add($productC);

        // Another bundle that includes A and the bundle above
        $bundle2 = new ProductBundle('School Pack');
        $bundle2->add($productA);
        $bundle2->add($bundle1);

        // Create the cart and add items
        $cart = new Cart();
        $cart->addItem($productA);     // 12.99
        $cart->addItem($bundle1);      // 1.99 + 5.49
        $cart->addItem($bundle2);      // 12.99 + (1.99 + 5.49)

        // Total: 12.99 + 7.48 + 20.47 = 40.94
        $this->assertEquals(40.94, $cart->getTotal());
    }
}
