<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Document;

interface DocumentRepositoryInterface
{
    public function documentOfId(int $documentId): ?Document;

    public function allDocuments(): array;

    public function store(Document $document): void;
}
