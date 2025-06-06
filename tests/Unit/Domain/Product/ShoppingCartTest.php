<?php

namespace App\Tests\Unit\Domain\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ShoppingCart;
use App\Domain\Discount\QuantityDiscountVisitor;
use App\Domain\Discount\SeasonalDiscountVisitor;
use PHPUnit\Framework\TestCase;

class ShoppingCartTest extends TestCase
{
    public function testApplyQuantityDiscount()
    {
        $product1 = new Product('Product 1', 100);
        $product2 = new Product('Product 2', 150);

        $cart = new ShoppingCart();
        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $quantityDiscount = new QuantityDiscountVisitor(10);

        $cart->applyDiscount($quantityDiscount);

        $this->expectOutputString("Original price: 100 | Discounted price: 90\nOriginal price: 150 | Discounted price: 135\n");
    }

    public function testApplySeasonalDiscount()
    {
        $product1 = new Product('Product 1', 200);
        $product2 = new Product('Product 2', 250);

        $cart = new ShoppingCart();
        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $seasonalDiscount = new SeasonalDiscountVisitor();

        $cart->applyDiscount($seasonalDiscount);

        $this->expectOutputString("Original price: 200 | Discounted price: 170\nOriginal price: 250 | Discounted price: 212.5\n");
    }

    public function testMultipleDiscounts()
    {
        $product1 = new Product('Product 1', 300);
        $product2 = new Product('Product 2', 450);

        $cart = new ShoppingCart();
        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $quantityDiscount = new QuantityDiscountVisitor(15);
        $seasonalDiscount = new SeasonalDiscountVisitor();

        $cart->applyDiscount($quantityDiscount);

        $cart->applyDiscount($seasonalDiscount);

        $this->expectOutputString(
            "Original price: 300 | Discounted price: 255\nOriginal price: 450 | Discounted price: 382.5\n" .
            "Original price: 300 | Discounted price: 255\nOriginal price: 450 | Discounted price: 382.5\n"
        );
    }

    public function testApplyDiscountWithNoProducts()
    {
        $cart = new ShoppingCart();

        $quantityDiscount = new QuantityDiscountVisitor(10);

        $cart->applyDiscount($quantityDiscount);

        $this->expectOutputString("");
    }
}
