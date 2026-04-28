<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
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
}
