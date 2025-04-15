<?php

namespace App\Tests\Application\Handler;

use App\Application\DTO\UpdateDocumentDTO;
use App\Application\Document\UpdateDocumentContentHandler;
use App\Application\Mapper\DocumentDTOMapper;
use App\Domain\Entity\Document;
use App\Domain\Repository\DocumentRepositoryInterface as DocumentRepository;
use PHPUnit\Framework\TestCase;

class UpdateDocumentContentHandlerTest extends TestCase
{
    public function testHandleUpdatesDocumentContentSuccessfully(): void
    {
        $dto = new UpdateDocumentDTO(1, 'Updated content');

        $document = $this->createMock(Document::class);
        $newContent = 'Updated content';

        $mapper = $this->createMock(DocumentDTOMapper::class);
        $mapper->expects($this->once())
            ->method('fromUpdateDTO')
            ->with($dto)
            ->willReturn([$document, $newContent]);

        $repository = $this->createMock(DocumentRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($document);

        $handler = new UpdateDocumentContentHandler($repository, $mapper);
        $handler->handle($dto);
    }
}
