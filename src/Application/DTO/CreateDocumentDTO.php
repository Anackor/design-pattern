<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateDocumentDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Title cannot be empty.')]
        #[Assert\Length(max: 255, maxMessage: 'Title cannot be longer than {{ limit }} characters.')]
        public readonly string $title,
        #[Assert\NotBlank(message: 'Content cannot be empty.')]
        public readonly string $initialContent,
        #[Assert\Positive(message: 'User ID must be positive.')]
        public readonly int $userId,
    ) {}
}
