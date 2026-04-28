<?php

namespace App\Tests\Unit\Application\Prototype;

use App\Application\Prototype\ProductCloner;
use App\Application\Prototype\ProductCloneOverrides;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductClonerTest extends TestCase
{
    public function testCloneWithOverridesAppliesProvidedValues(): void
    {
        $originalCategory = Category::named('Original Category');
        $newCategory = Category::named('New Category');
        $original = new Product('Original Product', 10.0, 'Original description', $originalCategory);

        $clone = (new ProductCloner())->cloneWithOverrides(
            $original,
            ProductCloneOverrides::fromScalars(
                'Cloned Product',
                12.5,
                'Updated description',
                $newCategory,
                true
            )
        );

        $this->assertNotSame($original, $clone);
        $this->assertSame('Original Product', $original->getName());
        $this->assertSame('Cloned Product', $clone->getName());
        $this->assertSame(1250, $clone->getPriceMoney()->amountInCents());
        $this->assertSame('Updated description', $clone->getDescription());
        $this->assertSame($newCategory, $clone->getCategory());
    }

    public function testCloneWithOverridesKeepsValuesThatWereNotProvided(): void
    {
        $category = Category::named('Original Category');
        $original = new Product('Original Product', 10.0, 'Original description', $category);

        $clone = (new ProductCloner())->cloneWithOverrides(
            $original,
            ProductCloneOverrides::fromScalars(description: 'Only description changed')
        );

        $this->assertSame('Original Product', $clone->getName());
        $this->assertSame(1000, $clone->getPriceMoney()->amountInCents());
        $this->assertSame('Only description changed', $clone->getDescription());
        $this->assertSame($category, $clone->getCategory());
    }

    public function testCloneOverridesRejectInvalidPriceEarly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative.');

        ProductCloneOverrides::fromScalars(price: -1.0);
    }
}
