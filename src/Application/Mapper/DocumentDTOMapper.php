<?php

namespace App\Application\Mapper;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\DTO\UpdateDocumentDTO;
use App\Domain\Repository\DocumentRepositoryInterface as DocumentRepository;
use App\Domain\Repository\UserRepositoryInterface as UserRepository;

/**
 * Responsible for transforming incoming DTOs into fully constructed domain entities.
 * This mapper handles the resolution of relationships such as converting scalar IDs into actual entity isntances
 * by interacting with repositories or services
 * 
 * Benefits of using mappers:
 * - Promotes the separation of concerns (SoC) by keeping DTOs simple and controllers clean.
 * - Centralizes the transformation logic, making it easier to test and mantain
 * - Improves flexibility allowing for consistent conversion strategies across the application.
 */
class DocumentDTOMapper
{
    public function __construct(
        private UserRepository $userRepository,
        private DocumentRepository $documentRepository
    ) {}

    public function fromCreateDTO(CreateDocumentDTO $dto): array
    {
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new \InvalidArgumentException('User not found.');
        }

        return [$dto->title, $dto->initialContent, $user];
    }

    public function fromUpdateDTO(UpdateDocumentDTO $dto): array
    {
        $document = $this->documentRepository->findById($dto->documentID);
        if (!$document) {
            throw new \InvalidArgumentException('Document not found.');
        }

        return [$document, $dto->newContent];
    }
}
