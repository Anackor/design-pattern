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

    public function findById(int $id): ?Category
    {
        return $this->entityManager->find(Category::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    public function save(Category $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
