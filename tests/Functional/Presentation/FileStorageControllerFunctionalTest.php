<?php

namespace App\Tests\Functional\Presentation;

use App\Application\DTO\FileOperationRequestDTO;
use App\Application\File\DeleteFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\UploadFileHandler;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class FileStorageControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testUploadReturnsSuccessEnvelopeThroughKernel(): void
    {
        $uploadHandler = $this->createMock(UploadFileHandler::class);
        $uploadHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (FileOperationRequestDTO $dto): bool {
                return 'local' === $dto->adapter
                    && '/tmp/file.txt' === $dto->path
                    && 'hello' === $dto->contents;
            }));

        $this->replaceFileHandlers(uploadHandler: $uploadHandler);

        $response = $this->requestJson('POST', '/file/upload', [
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
            'contents' => 'hello',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'data' => [],
        ], $this->decodeJson($response));
    }

    public function testUploadReturnsValidationErrorsForUnsupportedAdapter(): void
    {
        $uploadHandler = $this->createMock(UploadFileHandler::class);
        $uploadHandler->expects($this->never())->method('handle');

        $this->replaceFileHandlers(uploadHandler: $uploadHandler);

        $response = $this->requestJson('POST', '/file/upload', [
            'adapter' => 'azure',
            'path' => '/tmp/file.txt',
            'contents' => 'hello',
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('adapter', $payload['error']['details'][0]['field']);
        $this->assertStringContainsString('The value you selected is not a valid choice.', $payload['error']['details'][0]['message']);
    }

    public function testUploadReturnsStructuredBadRequestForInvalidJson(): void
    {
        $uploadHandler = $this->createMock(UploadFileHandler::class);
        $uploadHandler->expects($this->never())->method('handle');

        $this->replaceFileHandlers(uploadHandler: $uploadHandler);

        $response = $this->requestJson('POST', '/file/upload', '{"adapter":');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Invalid JSON payload.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testDownloadReturnsSuccessEnvelopeThroughKernel(): void
    {
        $downloadHandler = $this->createMock(DownloadFileHandler::class);
        $downloadHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (FileOperationRequestDTO $dto): bool {
                return 'local' === $dto->adapter
                    && '/tmp/file.txt' === $dto->path;
            }))
            ->willReturn('downloaded');

        $this->replaceFileHandlers(downloadHandler: $downloadHandler);

        $response = $this->request('GET', '/file/download', [
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
        ], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File downloaded successfully',
            'data' => ['contents' => 'downloaded'],
        ], $this->decodeJson($response));
    }

    public function testDownloadReturnsStructuredBadRequestWhenQueryIsMissing(): void
    {
        $downloadHandler = $this->createMock(DownloadFileHandler::class);
        $downloadHandler->expects($this->never())->method('handle');

        $this->replaceFileHandlers(downloadHandler: $downloadHandler);

        $response = $this->request('GET', '/file/download', [], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Missing required query parameters.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testDeleteReturnsSuccessEnvelopeThroughKernel(): void
    {
        $deleteHandler = $this->createMock(DeleteFileHandler::class);
        $deleteHandler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (FileOperationRequestDTO $dto): bool {
                return 'local' === $dto->adapter
                    && '/tmp/file.txt' === $dto->path;
            }));

        $this->replaceFileHandlers(deleteHandler: $deleteHandler);

        $response = $this->requestJson('DELETE', '/file/delete', [
            'adapter' => 'local',
            'path' => '/tmp/file.txt',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'File deleted successfully',
            'data' => [],
        ], $this->decodeJson($response));
    }

    private function replaceFileHandlers(
        ?UploadFileHandler $uploadHandler = null,
        ?DownloadFileHandler $downloadHandler = null,
        ?DeleteFileHandler $deleteHandler = null
    ): void {
        $this->setTestService(UploadFileHandler::class, $uploadHandler ?? $this->createMock(UploadFileHandler::class));
        $this->setTestService(DownloadFileHandler::class, $downloadHandler ?? $this->createMock(DownloadFileHandler::class));
        $this->setTestService(DeleteFileHandler::class, $deleteHandler ?? $this->createMock(DeleteFileHandler::class));
    }
}
