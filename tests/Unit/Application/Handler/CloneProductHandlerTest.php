<?php

namespace App\Tests\Unit\Application\Handler;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Product\CloneProductHandler;
use App\Application\Prototype\ProductCloner;
use App\Application\Prototype\ProductCloneOverrides;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Domain\Repository\CategoryRepositoryInterface as CategoryRepository;
use App\Domain\Repository\ProductRepositoryInterface as ProductRepository;
use PHPUnit\Framework\TestCase;

class CloneProductHandlerTest extends TestCase
{
    public function testCloneProductSuccessfully(): void
    {
        $original = new Product('Original Product', 10.0, 'Original desc', $this->buildCategory('Old Category'));

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('catalogProductOfId')->with(1)->willReturn($original);

        $category = $this->buildCategory('New Category');
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->method('catalogCategoryOfId')->with(2)->willReturn($category);

        $productRepository->expects($this->once())->method('addToCatalog');

        $dto = new ProductCloneDTO(
            1,
            'Cloned Product',
            12.5,
            'Updated description',
            2
        );

        $clonedProduct = new Product('Cloned Product', 12.5, 'Updated description', $this->buildCategory('New Category'));
        $cloner = $this->createMock(ProductCloner::class);
        $cloner->expects($this->once())
            ->method('cloneWithOverrides')
            ->with(
                $original,
                $this->callback(function (ProductCloneOverrides $overrides) use ($category): bool {
                    return $overrides->hasName()
                        && 'Cloned Product' === $overrides->name()
                        && $overrides->hasPrice()
                        && 1250 === $overrides->price()->amountInCents()
                        && $overrides->hasDescription()
                        && 'Updated description' === $overrides->description()
                        && $overrides->categoryWasProvided()
                        && $category === $overrides->category();
                })
            )
            ->willReturn($clonedProduct);

        $handler = new CloneProductHandler($productRepository, $categoryRepository, $cloner);
        $cloned = $handler->handle($dto);

        $this->assertNotSame($original, $cloned);
        $this->assertEquals('Cloned Product', $cloned->getName());
        $this->assertEquals(12.5, $cloned->getPrice());
        $this->assertSame(1250, $cloned->getPriceMoney()->amountInCents());
        $this->assertEquals('Updated description', $cloned->getDescription());
        $this->assertEquals($category, $cloned->getCategory());
    }

    public function testHandleRejectsMissingCategoryWhenCategoryOverrideRequested(): void
    {
        $original = new Product('Original Product', 10.0, 'Original desc', $this->buildCategory('Old Category'));

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('catalogProductOfId')->with(1)->willReturn($original);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->method('catalogCategoryOfId')->with(2)->willReturn(null);

        $cloner = $this->createMock(ProductCloner::class);
        $cloner->expects($this->never())->method('cloneWithOverrides');

        $handler = new CloneProductHandler($productRepository, $categoryRepository, $cloner);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category not found');

        $handler->handle(new ProductCloneDTO(1, categoryId: 2));
    }

    private function buildCategory(string $name): Category
    {
        return Category::named($name);
    }
}
