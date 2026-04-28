<?php

namespace App\Tests\Unit\Domain\Discount;

use App\Domain\Cart\ProductBundle;
use App\Domain\Cart\SingleProduct;
use App\Domain\Discount\DiscountCart;
use App\Domain\Discount\QuantityDiscountVisitor;
use App\Domain\Discount\SeasonalDiscountVisitor;
use PHPUnit\Framework\TestCase;

class DiscountCartTest extends TestCase
{
    public function testApplyQuantityDiscount(): void
    {
        $product1 = new SingleProduct('Product 1', 100);
        $product2 = new SingleProduct('Product 2', 150);

        $cart = new DiscountCart();
        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $quantityDiscount = new QuantityDiscountVisitor(10);

        $cart->applyDiscount($quantityDiscount);

        $this->expectOutputString("Original price: 100 | Discounted price: 90\nOriginal price: 150 | Discounted price: 135\n");
    }

    public function testApplySeasonalDiscount(): void
    {
        $product1 = new SingleProduct('Product 1', 200);
        $product2 = new SingleProduct('Product 2', 250);

        $cart = new DiscountCart();
        $cart->addProduct($product1);
        $cart->addProduct($product2);

        $seasonalDiscount = new SeasonalDiscountVisitor();

        $cart->applyDiscount($seasonalDiscount);

        $this->expectOutputString("Original price: 200 | Discounted price: 170\nOriginal price: 250 | Discounted price: 212.5\n");
    }

    public function testApplyDiscountToBundles(): void
    {
        $bundle = new ProductBundle('Bundle');
        $bundle->add(new SingleProduct('Product 1', 100));
        $bundle->add(new SingleProduct('Product 2', 50));

        $cart = new DiscountCart();
        $cart->addProduct($bundle);

        $cart->applyDiscount(new SeasonalDiscountVisitor());

        $this->expectOutputString("Original price: 150 | Discounted price: 127.5\n");
    }

    public function testApplyDiscountWithNoProducts(): void
    {
        $cart = new DiscountCart();

        $quantityDiscount = new QuantityDiscountVisitor(10);

        $cart->applyDiscount($quantityDiscount);

        $this->expectOutputString('');
    }
}
