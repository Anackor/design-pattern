<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testNamedCreatesValidCategory(): void
    {
        $category = Category::named('Office Supplies');

        $this->assertSame('Office Supplies', $category->getName());
    }

    public function testSetNameRejectsEmptyValue(): void
    {
        $category = new Category();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category name cannot be empty.');

        $category->setName('   ');
    }

    public function testAddAndRemoveProductKeepBidirectionalRelationConsistent(): void
    {
        $category = Category::named('Office Supplies');
        $product = new Product('Notebook', 5.50, 'A5 notebook', $category);

        $this->assertNull($category->getId());

        $category->addProduct($product);

        $this->assertCount(1, $category->getProducts());
        $this->assertSame($category, $product->getCategory());

        $category->removeProduct($product);

        $this->assertCount(0, $category->getProducts());
        $this->assertNull($product->getCategory());
    }
}
