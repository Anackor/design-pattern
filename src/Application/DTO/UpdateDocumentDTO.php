<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateDocumentDTO
{
    public function __construct(
        #[Assert\Positive(message: 'Document ID must be positive.')]
        public readonly int $documentID,
        #[Assert\NotBlank(message: 'Content cannot be empty.')]
        public readonly string $newContent
    ) {}
}
