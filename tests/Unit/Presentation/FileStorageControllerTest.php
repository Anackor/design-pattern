<?php

namespace App\Tests\Unit\Presentation;

use App\Application\File\DeleteFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\UploadFileHandler;
use App\Presentation\FileStorageController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileStorageControllerTest extends TestCase
{
    public function testUploadReturnsSuccessMessage(): void
    {
        $uploadHandler = $this->createMock(UploadFileHandler::class);
        $uploadHandler->expects($this->once())->method('handle');

        $controller = $this->createController($uploadHandler);
        $response = $controller->upload($this->jsonRequest([
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
            'contents' => 'hello',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'data' => [],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testUploadThrowsBadRequestWhenPayloadIsMissing(): void
    {
        $controller = $this->createController();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required parameters.');

        $controller->upload($this->jsonRequest(['adapter' => 'local']));
    }

    public function testDownloadReturnsFileContents(): void
    {
        $downloadHandler = $this->createMock(DownloadFileHandler::class);
        $downloadHandler->expects($this->once())
            ->method('handle')
            ->willReturn('downloaded');

        $controller = $this->createController(downloadHandler: $downloadHandler);
        $response = $controller->download(Request::create('/file/download', 'GET', [
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File downloaded successfully',
            'data' => ['contents' => 'downloaded'],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testDownloadThrowsBadRequestWhenQueryIsMissing(): void
    {
        $controller = $this->createController();

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required query parameters.');

        $controller->download(Request::create('/file/download', 'GET'));
    }

    public function testDeleteReturnsSuccessMessage(): void
    {
        $deleteHandler = $this->createMock(DeleteFileHandler::class);
        $deleteHandler->expects($this->once())->method('handle');

        $controller = $this->createController(deleteHandler: $deleteHandler);
        $response = $controller->delete($this->jsonRequest([
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File deleted successfully',
            'data' => [],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testValidationErrorsAreReportedAsStructuredResponse(): void
    {
        $controller = $this->createController(
            validator: $this->createValidator($this->violationList('adapter', 'Unsupported adapter.'))
        );

        $response = $controller->upload($this->jsonRequest([
            'adapter' => 'azure',
            'path' => '/tmp/file.txt',
            'contents' => 'hello',
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'adapter', 'message' => 'Unsupported adapter.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    private function createController(
        ?UploadFileHandler $uploadHandler = null,
        ?DownloadFileHandler $downloadHandler = null,
        ?DeleteFileHandler $deleteHandler = null,
        ?ValidatorInterface $validator = null
    ): FileStorageController {
        return new FileStorageController(
            $uploadHandler ?? $this->createMock(UploadFileHandler::class),
            $downloadHandler ?? $this->createMock(DownloadFileHandler::class),
            $deleteHandler ?? $this->createMock(DeleteFileHandler::class),
            $validator ?? $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
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

    private function violationList(string $propertyPath, string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, $propertyPath, null),
        ]);
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
