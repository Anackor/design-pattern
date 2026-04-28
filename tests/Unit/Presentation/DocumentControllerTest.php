<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Document\CreateDocumentHandler;
use App\Application\Document\UpdateDocumentContentHandler;
use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Entity\User;
use App\Presentation\DocumentController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentControllerTest extends TestCase
{
    public function testCreateReturnsDocumentResponse(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));

        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->expects($this->once())->method('handle')->willReturn($document);

        $controller = new DocumentController($this->createValidator());
        $response = $controller->create($this->jsonRequest([
            'title' => 'Contract',
            'content' => 'Initial content',
            'userID' => 7,
        ]), $handler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['document' => []], json_decode((string) $response->getContent(), true));
    }

    public function testCreateReturnsBadRequestWhenHandlerThrows(): void
    {
        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('User not found.'));

        $controller = new DocumentController($this->createValidator());
        $response = $controller->create($this->jsonRequest([
            'title' => 'Contract',
            'content' => 'Initial content',
            'userID' => 7,
        ]), $handler);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['error' => 'User not found.'], json_decode((string) $response->getContent(), true));
    }

    public function testUpdateReturnsVersionResponse(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));
        $version = new DocumentVersion($document, 'Updated content');

        $handler = $this->createMock(UpdateDocumentContentHandler::class);
        $handler->expects($this->once())->method('handle')->willReturn($version);

        $controller = new DocumentController($this->createValidator());
        $response = $controller->update(9, $this->jsonRequest(['content' => 'Updated content']), $handler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['version' => []], json_decode((string) $response->getContent(), true));
    }

    public function testUpdateReturnsValidationErrors(): void
    {
        $controller = new DocumentController($this->createValidator($this->violationList('Invalid content.')));
        $handler = $this->createMock(UpdateDocumentContentHandler::class);

        $response = $controller->update(9, $this->jsonRequest(['content' => '']), $handler);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid content.', (string) $response->getContent());
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'payload', null),
        ]);
    }
}
