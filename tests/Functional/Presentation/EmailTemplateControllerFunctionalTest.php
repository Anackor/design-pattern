<?php

namespace App\Tests\Functional\Presentation;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Template\RenderEmailTemplateHandler;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class EmailTemplateControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testRenderReturnsTemplateContentThroughKernel(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (RenderTemplateDTO $dto): bool {
                return 'welcome' === $dto->templateKey
                    && ['name' => 'Alice'] === $dto->payload;
            }))
            ->willReturn('<h1>Hello Alice</h1>');

        $this->setTestService(RenderEmailTemplateHandler::class, $handler);

        $response = $this->requestJson('POST', '/api/render-template', [
            'templateKey' => 'welcome',
            'payload' => ['name' => 'Alice'],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Template rendered',
            'data' => ['content' => '<h1>Hello Alice</h1>'],
        ], $this->decodeJson($response));
    }

    public function testRenderReturnsStructuredBadRequestWhenTemplateKeyIsMissing(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(RenderEmailTemplateHandler::class, $handler);

        $response = $this->requestJson('POST', '/api/render-template', [
            'payload' => ['name' => 'Alice'],
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Missing required parameters.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testRenderReturnsStructuredBadRequestForInvalidJson(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(RenderEmailTemplateHandler::class, $handler);

        $response = $this->requestJson('POST', '/api/render-template', '{"templateKey":');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Invalid JSON payload.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testRenderReturnsStructuredBadRequestWhenHandlerFails(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willThrowException(new \InvalidArgumentException('Template not found.'));

        $this->setTestService(RenderEmailTemplateHandler::class, $handler);

        $response = $this->requestJson('POST', '/api/render-template', [
            'templateKey' => 'missing',
            'payload' => [],
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Template not found.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }
}
