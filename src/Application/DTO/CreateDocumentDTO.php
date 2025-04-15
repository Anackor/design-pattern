<?php

namespace App\Application\DTO;

use App\Domain\Entity\User;

class CreateDocumentDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $initialContent,
        public readonly int $userId,
    ) {
        
    }
}
