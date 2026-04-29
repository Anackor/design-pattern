<?php

namespace App\Tests\Functional\Presentation;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class NotificationControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testSendNotificationReturnsSuccessThroughKernel(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (NotificationRequestDTO $dto): bool {
                return 'Hello' === $dto->getTitle()
                    && 'Body' === $dto->getMessage()
                    && 'mail@example.com' === $dto->getReceiver()
                    && 'email' === $dto->getChannel();
            }));

        $this->setTestService(SendNotificationHandler::class, $handler);

        $response = $this->requestJson('POST', '/send-notification', [
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
            'channel' => 'email',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'data' => [],
        ], $this->decodeJson($response));
    }

    public function testSendNotificationReturnsJsonBadRequestWhenRequiredFieldsAreMissing(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(SendNotificationHandler::class, $handler);

        $response = $this->requestJson('POST', '/send-notification', [
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Missing required parameters.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testSendNotificationReturnsValidationErrorsForInvalidChannel(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(SendNotificationHandler::class, $handler);

        $response = $this->requestJson('POST', '/send-notification', [
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
            'channel' => 'pagerduty',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $payload = $this->decodeJson($response);
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('channel', $payload['error']['details'][0]['field']);
        $this->assertStringContainsString('Invalid channel', $payload['error']['details'][0]['message']);
        $this->assertStringContainsString('pagerduty', $payload['error']['details'][0]['message']);
    }

    public function testSendNotificationReturnsJsonBadRequestForInvalidJson(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(SendNotificationHandler::class, $handler);

        $response = $this->requestJson('POST', '/send-notification', '{"title":');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Invalid JSON payload.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }
}
