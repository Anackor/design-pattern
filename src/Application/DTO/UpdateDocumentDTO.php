<?php

namespace App\Application\DTO;

class UpdateDocumentDTO
{
    public function __construct(
        public readonly int $documentID,
        public readonly string $newContent
    ) {}
}
