<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Document;
use App\Domain\Repository\DocumentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineDocumentRepository implements DocumentRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findById(int $id): ?Document
    {
        return $this->entityManager->find(Document::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Document::class)->findAll();
    }

    public function save(Document $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
