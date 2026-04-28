<?php

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\FileStorageService;
use App\Domain\Adapter\FileStorageInterface;
use PHPUnit\Framework\TestCase;

class FileStorageServiceTest extends TestCase
{
    public function testUploadFileDelegatesToStorage(): void
    {
        $storage = $this->createMock(FileStorageInterface::class);
        $storage->expects($this->once())
            ->method('upload')
            ->with('/tmp/file.txt', 'content')
            ->willReturn(true);

        $service = new FileStorageService($storage);

        $this->assertTrue($service->uploadFile('/tmp/file.txt', 'content'));
    }

    public function testDownloadFileDelegatesToStorage(): void
    {
        $storage = $this->createMock(FileStorageInterface::class);
        $storage->expects($this->once())
            ->method('download')
            ->with('/tmp/file.txt')
            ->willReturn('downloaded');

        $service = new FileStorageService($storage);

        $this->assertSame('downloaded', $service->downloadFile('/tmp/file.txt'));
    }

    public function testDeleteFileDelegatesToStorage(): void
    {
        $storage = $this->createMock(FileStorageInterface::class);
        $storage->expects($this->once())
            ->method('delete')
            ->with('/tmp/file.txt')
            ->willReturn(true);

        $service = new FileStorageService($storage);

        $this->assertTrue($service->deleteFile('/tmp/file.txt'));
    }
}
