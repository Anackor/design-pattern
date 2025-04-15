<?php

namespace App\Application\Document;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\Mapper\DocumentDTOMapper;
use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Repository\DocumentRepositoryInterface as DocumentRepository;

class CreateDocumentHandler
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentDTOMapper $mapper
    ) {}

    public function handle(CreateDocumentDTO $dto): Document
    {
        [$title, $content, $user] = $this->mapper->fromCreateDTO($dto);

        $document = new Document($title, $user);
        $document->addVersion(new DocumentVersion($document, $content));

        $this->documentRepository->save($document);

        return $document;
    }
}
