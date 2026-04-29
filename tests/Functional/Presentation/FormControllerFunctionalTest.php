<?php

namespace App\Tests\Functional\Presentation;

use App\Application\BuildForm\BuildFormHandler;
use App\Application\DTO\FormRequestDTO;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class FormControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testBuildReturnsRenderedFormThroughKernel(): void
    {
        $handler = $this->createMock(BuildFormHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (FormRequestDTO $dto): bool {
                return 'html' === $dto->type
                    && 'Email' === $dto->textFieldLabel
                    && 'Terms' === $dto->checkboxLabel
                    && 'Submit' === $dto->buttonLabel;
            }))
            ->willReturn('<form>Rendered</form>');

        $this->setTestService(BuildFormHandler::class, $handler);

        $response = $this->requestJson('POST', '/form', [
            'type' => 'html',
            'textFieldLabel' => 'Email',
            'checkboxLabel' => 'Terms',
            'buttonLabel' => 'Submit',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Form rendered',
            'data' => ['form' => '<form>Rendered</form>'],
        ], $this->decodeJson($response));
    }

    public function testBuildReturnsValidationErrorsForUnsupportedType(): void
    {
        $handler = $this->createMock(BuildFormHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(BuildFormHandler::class, $handler);

        $response = $this->requestJson('POST', '/form', [
            'type' => 'pdf',
            'textFieldLabel' => 'Email',
            'checkboxLabel' => 'Terms',
            'buttonLabel' => 'Submit',
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('type', $payload['error']['details'][0]['field']);
        $this->assertStringContainsString('The value you selected is not a valid choice.', $payload['error']['details'][0]['message']);
    }

    public function testBuildReturnsStructuredBadRequestForInvalidJson(): void
    {
        $handler = $this->createMock(BuildFormHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(BuildFormHandler::class, $handler);

        $response = $this->requestJson('POST', '/form', '{"type":');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Invalid JSON payload.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }
}
