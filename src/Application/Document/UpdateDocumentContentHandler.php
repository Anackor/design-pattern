<?php

namespace App\Application\Document;

use App\Application\DTO\UpdateDocumentDTO;
use App\Application\Mapper\DocumentDTOMapper;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Repository\DocumentRepositoryInterface as DocumentRepository;

/**
 * In this implementation, instead of modifying the existing `DocumentVersion` directly, a new `DocumentVersion` is created
 * every time content is updated. This ensures immutability of the `DocumentVersion` object.
 *
 * - The document itself may have its content updated via a method like `withContent()`, but the `DocumentVersion` object is
 *   never modified after it is created.
 * - Each time a change occurs, a new instance of `DocumentVersion` is created, reflecting the new content while preserving
 *   the original version.
 *
 * This pattern guarantees that the state of previous document versions remains unchanged, allowing for consistent versioning
 * and historical tracking of document changes.
 */
class UpdateDocumentContentHandler
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentDTOMapper $mapper
    ) {}

    public function handle(UpdateDocumentDTO $dto): DocumentVersion
    {
        [$document, $newContent] = $this->mapper->fromUpdateDTO($dto);

        $version = new DocumentVersion($document, $newContent);
        $this->documentRepository->store($document);

        return $version;
    }
}
