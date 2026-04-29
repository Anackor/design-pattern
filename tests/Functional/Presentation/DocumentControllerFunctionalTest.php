<?php

namespace App\Tests\Functional\Presentation;

use App\Application\Document\CreateDocumentHandler;
use App\Application\Document\UpdateDocumentContentHandler;
use App\Application\DTO\CreateDocumentDTO;
use App\Application\DTO\UpdateDocumentDTO;
use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Domain\Entity\User;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class DocumentControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testCreateReturnsStructuredDocumentEnvelopeThroughKernel(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));
        new DocumentVersion($document, 'Initial content');

        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (CreateDocumentDTO $dto): bool {
                return 'Contract' === $dto->title
                    && 'Initial content' === $dto->initialContent
                    && 7 === $dto->userId;
            }))
            ->willReturn($document);

        $this->setTestService(CreateDocumentHandler::class, $handler);

        $response = $this->requestJson('POST', '/documents', [
            'title' => 'Contract',
            'content' => 'Initial content',
            'userID' => 7,
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Document created',
            'data' => [
                'document' => [
                    'id' => null,
                    'title' => 'Contract',
                    'version_count' => 1,
                ],
            ],
        ], $this->decodeJson($response));
    }

    public function testCreateReturnsValidationErrorsForBlankTitle(): void
    {
        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(CreateDocumentHandler::class, $handler);

        $response = $this->requestJson('POST', '/documents', [
            'title' => '',
            'content' => 'Initial content',
            'userID' => 7,
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('title', $payload['error']['details'][0]['field']);
        $this->assertSame('Title cannot be empty.', $payload['error']['details'][0]['message']);
    }

    public function testCreateReturnsStructuredBadRequestWhenPayloadIsMissing(): void
    {
        $handler = $this->createMock(CreateDocumentHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(CreateDocumentHandler::class, $handler);

        $response = $this->requestJson('POST', '/documents', [
            'title' => 'Contract',
            'content' => 'Initial content',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Missing required parameters.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testUpdateReturnsStructuredVersionEnvelopeThroughKernel(): void
    {
        $document = new Document('Contract', User::register('Doc Owner', 'doc@example.com'));
        $version = new DocumentVersion($document, 'Updated content');

        $handler = $this->createMock(UpdateDocumentContentHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (UpdateDocumentDTO $dto): bool {
                return 9 === $dto->documentID
                    && 'Updated content' === $dto->newContent;
            }))
            ->willReturn($version);

        $this->setTestService(UpdateDocumentContentHandler::class, $handler);

        $response = $this->requestJson('PUT', '/documents/9/content', [
            'content' => 'Updated content',
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('success', $payload['status']);
        $this->assertSame('Document content updated', $payload['message']);
        $this->assertSame(null, $payload['data']['version']['id']);
        $this->assertSame(null, $payload['data']['version']['document_id']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{6}$/', $payload['data']['version']['version_code']);
        $this->assertNotEmpty($payload['data']['version']['created_at']);
    }

    public function testUpdateReturnsValidationErrorsForBlankContent(): void
    {
        $handler = $this->createMock(UpdateDocumentContentHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(UpdateDocumentContentHandler::class, $handler);

        $response = $this->requestJson('PUT', '/documents/9/content', [
            'content' => '',
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('newContent', $payload['error']['details'][0]['field']);
        $this->assertSame('Content cannot be empty.', $payload['error']['details'][0]['message']);
    }
}
