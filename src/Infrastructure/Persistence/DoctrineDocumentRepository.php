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

    public function documentOfId(int $documentId): ?Document
    {
        return $this->entityManager->find(Document::class, $documentId);
    }

    public function allDocuments(): array
    {
        return $this->entityManager->getRepository(Document::class)->findAll();
    }

    public function store(Document $document): void
    {
        $this->entityManager->persist($document);
        $this->entityManager->flush();
    }
}
