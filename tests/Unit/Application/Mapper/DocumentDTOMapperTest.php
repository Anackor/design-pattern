<?php

namespace App\Tests\Unit\Application\Mapper;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\DTO\UpdateDocumentDTO;
use App\Application\Mapper\DocumentDTOMapper;
use App\Domain\Entity\Document;
use App\Domain\Entity\User;
use App\Domain\Repository\DocumentRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DocumentDTOMapperTest extends TestCase
{
    public function testFromCreateDtoReturnsNormalizedTuple(): void
    {
        $user = User::register('Doc Owner', 'doc-owner@example.com');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('registeredUserOfId')
            ->with(7)
            ->willReturn($user);

        $documentRepository = $this->createMock(DocumentRepositoryInterface::class);

        $mapper = new DocumentDTOMapper($userRepository, $documentRepository);
        $result = $mapper->fromCreateDTO(new CreateDocumentDTO('Contract', 'Initial content', 7));

        $this->assertSame(['Contract', 'Initial content', $user], $result);
    }

    public function testFromCreateDtoRejectsUnknownUser(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->method('registeredUserOfId')->willReturn(null);

        $mapper = new DocumentDTOMapper($userRepository, $this->createMock(DocumentRepositoryInterface::class));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User not found.');

        $mapper->fromCreateDTO(new CreateDocumentDTO('Contract', 'Initial content', 7));
    }

    public function testFromUpdateDtoReturnsDocumentAndContent(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc-owner@example.com'));

        $documentRepository = $this->createMock(DocumentRepositoryInterface::class);
        $documentRepository->expects($this->once())
            ->method('documentOfId')
            ->with(9)
            ->willReturn($document);

        $mapper = new DocumentDTOMapper($this->createMock(UserRepositoryInterface::class), $documentRepository);
        $result = $mapper->fromUpdateDTO(new UpdateDocumentDTO(9, 'Updated content'));

        $this->assertSame([$document, 'Updated content'], $result);
    }

    public function testFromUpdateDtoRejectsUnknownDocument(): void
    {
        $documentRepository = $this->createMock(DocumentRepositoryInterface::class);
        $documentRepository->method('documentOfId')->willReturn(null);

        $mapper = new DocumentDTOMapper($this->createMock(UserRepositoryInterface::class), $documentRepository);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Document not found.');

        $mapper->fromUpdateDTO(new UpdateDocumentDTO(9, 'Updated content'));
    }
}
