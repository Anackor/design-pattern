<?php

namespace App\Tests\Unit\Presentation;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Presentation\NotificationController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationControllerTest extends TestCase
{
    public function testSendNotificationBuildsDtoWithCorrectReceiverAndMessage(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (NotificationRequestDTO $dto): bool {
                return $dto->getTitle() === 'Hello'
                    && $dto->getMessage() === 'Body'
                    && $dto->getReceiver() === 'mail@example.com'
                    && $dto->getChannel() === 'email';
            }));

        $controller = new NotificationController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->sendNotification($this->jsonRequest([
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
            'channel' => 'email',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Notification sent successfully',
            'data' => [],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testSendNotificationThrowsBadRequestWhenPayloadIsMissing(): void
    {
        $controller = new NotificationController(
            $this->createMock(SendNotificationHandler::class),
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required parameters.');

        $controller->sendNotification($this->jsonRequest(['title' => 'Hello']));
    }

    public function testSendNotificationReturnsValidationErrors(): void
    {
        $controller = new NotificationController(
            $this->createMock(SendNotificationHandler::class),
            $this->createValidator($this->violationList('Invalid channel.')),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );

        $response = $controller->sendNotification($this->jsonRequest([
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
            'channel' => 'pagerduty',
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'payload', 'message' => 'Invalid channel.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testSendNotificationReturnsServerErrorWhenHandlerFails(): void
    {
        $handler = $this->createMock(SendNotificationHandler::class);
        $handler->method('handle')->willThrowException(new \RuntimeException('Transport unavailable.'));

        $controller = new NotificationController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->sendNotification($this->jsonRequest([
            'title' => 'Hello',
            'receiver' => 'mail@example.com',
            'message' => 'Body',
            'channel' => 'email',
        ]));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Transport unavailable.',
            'error' => ['type' => 'internal_server_error'],
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
