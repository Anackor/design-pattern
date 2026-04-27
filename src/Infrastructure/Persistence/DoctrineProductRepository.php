<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function catalogProductOfId(int $productId): ?Product
    {
        return $this->entityManager->find(Product::class, $productId);
    }

    public function allCatalogProducts(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    public function addToCatalog(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
