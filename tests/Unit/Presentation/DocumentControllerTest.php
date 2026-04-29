<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Document\CreateDocumentHandler;
use App\Application\Document\UpdateDocumentContentHandler;
use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Entity\User;
use App\Presentation\DocumentController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
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

        $controller = new DocumentController($this->createValidator(), new JsonRequestDecoder(), $this->apiResponseFactory());
        $response = $controller->create($this->jsonRequest([
            'title' => 'Contract',
            'content' => 'Initial content',
            'userID' => 7,
        ]), $handler);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Document created',
            'data' => ['document' => [
                'id' => null,
                'title' => 'Contract',
                'version_count' => 0,
            ]],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCreateReturnsBadRequestWhenHandlerThrows(): void
    {
        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('User not found.'));

        $controller = new DocumentController($this->createValidator(), new JsonRequestDecoder(), $this->apiResponseFactory());
        $response = $controller->create($this->jsonRequest([
            'title' => 'Contract',
            'content' => 'Initial content',
            'userID' => 7,
        ]), $handler);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found.',
            'error' => ['type' => 'bad_request'],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testUpdateReturnsVersionResponse(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));
        $version = new DocumentVersion($document, 'Updated content');

        $handler = $this->createMock(UpdateDocumentContentHandler::class);
        $handler->expects($this->once())->method('handle')->willReturn($version);

        $controller = new DocumentController($this->createValidator(), new JsonRequestDecoder(), $this->apiResponseFactory());
        $response = $controller->update(9, $this->jsonRequest(['content' => 'Updated content']), $handler);
        $payload = json_decode((string) $response->getContent(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $payload['status']);
        $this->assertSame('Document content updated', $payload['message']);
        $this->assertSame(null, $payload['data']['version']['id']);
        $this->assertSame(null, $payload['data']['version']['document_id']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{6}$/', $payload['data']['version']['version_code']);
        $this->assertNotEmpty($payload['data']['version']['created_at']);
    }

    public function testUpdateReturnsValidationErrors(): void
    {
        $controller = new DocumentController(
            $this->createValidator($this->violationList('Invalid content.')),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $handler = $this->createMock(UpdateDocumentContentHandler::class);

        $response = $controller->update(9, $this->jsonRequest(['content' => '']), $handler);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'payload', 'message' => 'Invalid content.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode([] === $data ? new \stdClass() : $data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'payload', null),
        ]);
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
