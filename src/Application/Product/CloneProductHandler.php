<?php

namespace App\Application\Product;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Prototype\ProductCloner;
use App\Application\Prototype\ProductCloneOverrides;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface as ProductRepository;
use App\Domain\Repository\CategoryRepositoryInterface as CategoryRepository;

class CloneProductHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
        private ProductCloner $cloner
    ) {}

    public function handle(ProductCloneDTO $dto): Product
    {
        $original = $this->productRepository->catalogProductOfId($dto->originalId);
        if (!$original) {
            throw new \InvalidArgumentException('Product not found');
        }

        $category = null;
        if (null !== $dto->categoryId) {
            $category = $this->categoryRepository->catalogCategoryOfId($dto->categoryId);

            if (null === $category) {
                throw new \InvalidArgumentException('Category not found');
            }
        }

        $cloned = $this->cloner->cloneWithOverrides(
            $original,
            ProductCloneOverrides::fromScalars(
                $dto->name,
                $dto->price,
                $dto->description,
                $category,
                null !== $dto->categoryId
            )
        );
        $this->productRepository->addToCatalog($cloned);

        return $cloned;
    }
}
