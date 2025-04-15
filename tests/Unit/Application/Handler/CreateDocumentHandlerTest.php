<?php

namespace App\Tests\Application\Document;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\Document\CreateDocumentHandler;
use App\Application\Mapper\DocumentDTOMapper;
use App\Domain\Entity\Document;
use App\Domain\Entity\User;
use App\Domain\Repository\DocumentRepositoryInterface as DocumentRepository;
use PHPUnit\Framework\TestCase;

class CreateDocumentHandlerTest extends TestCase
{
    public function testHandleCreatesDocumentSuccessfully(): void
    {
        $dto = new CreateDocumentDTO('Test Title', 'Initial Content', 1);

        $user = $this->createMock(User::class);
        $expectedTitle = 'Test Title';
        $expectedContent = 'Initial Content';

        $mapper = $this->createMock(DocumentDTOMapper::class);
        $mapper->expects($this->once())
            ->method('fromCreateDTO')
            ->with($dto)
            ->willReturn([$expectedTitle, $expectedContent, $user]);

        $repository = $this->createMock(DocumentRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($document) use ($expectedTitle, $expectedContent, $user) {
                return $document instanceof Document
                    && $document->getTitle() === $expectedTitle
                    && $document->getUser() === $user
                    && $document->getLastVersion()?->getContent() === $expectedContent;
            }));

        $handler = new CreateDocumentHandler($repository, $mapper);
        $result = $handler->handle($dto);

        $this->assertInstanceOf(Document::class, $result);
        $this->assertEquals($expectedTitle, $result->getTitle());
        $this->assertEquals($user, $result->getUser());
        $this->assertEquals($expectedContent, $result->getLastVersion()?->getContent());
    }
}
