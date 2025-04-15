<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Document;

interface DocumentRepositoryInterface
{
    public function findById(int $id): ?Document;
    public function findAll(): array;
    public function save(Document $user): void;
}
