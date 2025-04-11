<?php

namespace App\Tests\Application\Handler;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Product\CloneProductHandler;
use App\Application\Prototype\ProductCloner;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Domain\Repository\CategoryRepositoryInterface as CategoryRepository;
use App\Domain\Repository\ProductRepositoryInterface as ProductRepository;
use PHPUnit\Framework\TestCase;

class CloneProductHandlerTest extends TestCase
{
    public function testCloneProductSuccessfully(): void
    {
        $original = new Product('Original Product', 10.0, 'Original desc', new Category('Old Category'));

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('findById')->with(1)->willReturn($original);

        $category = new Category('New Category');
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->method('findById')->with(2)->willReturn($category);

        $productRepository->expects($this->once())->method('save');

        $dto = new ProductCloneDTO(
            1,
            'Cloned Product',
            12.5,
            'Updated description',
            2
        );

        $clonedProduct = new Product('Cloned Product', 12.5, 'Updated description', new Category('New Category'));
        $cloner = $this->createMock(ProductCloner::class);
        $cloner->method('cloneWithOverrides')->willReturn($clonedProduct);
        
        $handler = new CloneProductHandler($productRepository, $categoryRepository, $cloner);
        $cloned = $handler->handle($dto);

        $this->assertNotSame($original, $cloned);
        $this->assertEquals('Cloned Product', $cloned->getName());
        $this->assertEquals(12.5, $cloned->getPrice());
        $this->assertEquals('Updated description', $cloned->getDescription());
        $this->assertEquals($category, $cloned->getCategory());
    }
}
