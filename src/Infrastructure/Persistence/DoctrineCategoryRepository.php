<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCategoryRepository implements CategoryRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function catalogCategoryOfId(int $categoryId): ?Category
    {
        return $this->entityManager->find(Category::class, $categoryId);
    }

    public function allCatalogCategories(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    public function addToCatalog(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}
