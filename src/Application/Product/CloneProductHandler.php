<?php

namespace App\Application\Product;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Prototype\ProductCloner;
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
        $original = $this->productRepository->findById($dto->originalId);
        if (!$original) {
            throw new \InvalidArgumentException("Product not found");
        }

        $category = $dto->categoryId
            ? $this->categoryRepository->findById($dto->categoryId)
            : null;

        $cloned = $this->cloner->cloneWithOverrides($original, [
            'name' => $dto->name,
            'price' => $dto->price,
            'description' => $dto->description,
            'category' => $category,
        ]);
        $this->productRepository->save($cloned);

        return $cloned;
    }
}
