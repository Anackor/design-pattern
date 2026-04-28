<?php

namespace App\Tests\Unit\Domain\Cart;

use App\Domain\Cart\SingleProduct;
use PHPUnit\Framework\TestCase;

class SingleProductTest extends TestCase
{
    public function testLeafExposesNameAndMoneyPrice(): void
    {
        $product = new SingleProduct('Notebook', 12.99);

        $this->assertSame('Notebook', $product->getName());
        $this->assertSame(1299, $product->getPrice()->amountInCents());
        $this->assertSame('EUR', $product->getPrice()->currency());
    }
}
