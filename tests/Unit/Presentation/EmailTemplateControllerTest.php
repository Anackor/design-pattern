<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Template\RenderEmailTemplateHandler;
use App\Presentation\EmailTemplateController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class EmailTemplateControllerTest extends TestCase
{
    public function testRenderReturnsRenderedTemplateContent(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn('<h1>Hello</h1>');

        $controller = new EmailTemplateController(new JsonRequestDecoder(), $this->apiResponseFactory());
        $response = $controller->render($this->jsonRequest([
            'templateKey' => 'welcome',
            'payload' => ['name' => 'Alice'],
        ]), $handler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Template rendered',
            'data' => ['content' => '<h1>Hello</h1>'],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testRenderReturnsBadRequestWhenHandlerFails(): void
    {
        $handler = $this->createMock(RenderEmailTemplateHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('Template not found.'));

        $controller = new EmailTemplateController(new JsonRequestDecoder(), $this->apiResponseFactory());
        $response = $controller->render($this->jsonRequest([
            'templateKey' => 'missing',
            'payload' => [],
        ]), $handler);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Template not found.',
            'error' => ['type' => 'bad_request'],
        ], json_decode((string) $response->getContent(), true));
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
