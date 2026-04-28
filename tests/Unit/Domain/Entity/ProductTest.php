<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Shared\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductRejectsEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty.');

        new Product('   ', 10.0, 'Description', $this->buildCategory());
    }

    public function testProductRejectsNegativePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product price cannot be negative.');

        new Product('Product', -1.0, 'Description', $this->buildCategory());
    }

    public function testProductRejectsEmptyDescription(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product description cannot be empty.');

        new Product('Product', 10.0, '   ', $this->buildCategory());
    }

    public function testSetPriceRejectsNegativeValue(): void
    {
        $product = new Product('Product', 10.0, 'Description', $this->buildCategory());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product price cannot be negative.');

        $product->setPrice(-5.0);
    }

    public function testGetPriceMoneyReturnsValueObject(): void
    {
        $product = new Product('Product', 10.5, 'Description', $this->buildCategory());

        $this->assertTrue($product->getPriceMoney()->equals(Money::fromFloat(10.5)));
    }

    public function testUpdatePriceAcceptsMoney(): void
    {
        $product = new Product('Product', 10.0, 'Description', $this->buildCategory());

        $product->updatePrice(Money::fromFloat(12.5));

        $this->assertSame(12.5, $product->getPrice());
        $this->assertSame(1250, $product->getPriceMoney()->amountInCents());
    }

    private function buildCategory(): Category
    {
        return Category::named('Category');
    }
}
