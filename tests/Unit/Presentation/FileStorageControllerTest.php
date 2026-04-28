<?php

namespace App\Tests\Unit\Presentation;

use App\Application\File\DeleteFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\UploadFileHandler;
use App\Presentation\FileStorageController;
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
        $this->assertSame(['message' => 'File uploaded successfully'], json_decode((string) $response->getContent(), true));
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
        $this->assertSame(['contents' => 'downloaded'], json_decode((string) $response->getContent(), true));
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
        $this->assertSame(['message' => 'File deleted successfully'], json_decode((string) $response->getContent(), true));
    }

    public function testValidationErrorsAreReportedAsBadRequest(): void
    {
        $controller = $this->createController(
            validator: $this->createValidator($this->violationList('adapter', 'Unsupported adapter.'))
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('adapter: Unsupported adapter.');

        $controller->upload($this->jsonRequest([
            'adapter' => 'azure',
            'path' => '/tmp/file.txt',
            'contents' => 'hello',
        ]));
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
            $validator ?? $this->createValidator()
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
        return new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $propertyPath, string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, $propertyPath, null),
        ]);
    }
}
