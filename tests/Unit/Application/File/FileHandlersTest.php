<?php

namespace App\Tests\Unit\Application\File;

use App\Application\DTO\FileOperationRequestDTO;
use App\Application\File\DeleteFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\FileStorageResolverInterface;
use App\Application\File\UploadFileHandler;
use App\Domain\Adapter\FileStorageInterface;
use PHPUnit\Framework\TestCase;

class FileHandlersTest extends TestCase
{
    public function testUploadHandlerDelegatesToResolvedAdapter(): void
    {
        $adapter = $this->createMock(FileStorageInterface::class);
        $adapter->expects($this->once())
            ->method('upload')
            ->with('/tmp/report.txt', 'content');

        $resolver = $this->createMock(FileStorageResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->with('local')
            ->willReturn($adapter);

        $handler = new UploadFileHandler($resolver);
        $handler->handle(new FileOperationRequestDTO('local', '/tmp/report.txt', 'content'));
    }

    public function testUploadHandlerUsesEmptyStringWhenContentsAreNull(): void
    {
        $adapter = $this->createMock(FileStorageInterface::class);
        $adapter->expects($this->once())
            ->method('upload')
            ->with('/tmp/empty.txt', '');

        $resolver = $this->createMock(FileStorageResolverInterface::class);
        $resolver->method('resolve')->willReturn($adapter);

        $handler = new UploadFileHandler($resolver);
        $handler->handle(new FileOperationRequestDTO('local', '/tmp/empty.txt'));
    }

    public function testDownloadHandlerReturnsAdapterContents(): void
    {
        $adapter = $this->createMock(FileStorageInterface::class);
        $adapter->expects($this->once())
            ->method('download')
            ->with('/tmp/report.txt')
            ->willReturn('downloaded content');

        $resolver = $this->createMock(FileStorageResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->with('ftp')
            ->willReturn($adapter);

        $handler = new DownloadFileHandler($resolver);

        $this->assertSame(
            'downloaded content',
            $handler->handle(new FileOperationRequestDTO('ftp', '/tmp/report.txt'))
        );
    }

    public function testDeleteHandlerDelegatesToResolvedAdapter(): void
    {
        $adapter = $this->createMock(FileStorageInterface::class);
        $adapter->expects($this->once())
            ->method('delete')
            ->with('/tmp/old-report.txt');

        $resolver = $this->createMock(FileStorageResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->with('aws')
            ->willReturn($adapter);

        $handler = new DeleteFileHandler($resolver);
        $handler->handle(new FileOperationRequestDTO('aws', '/tmp/old-report.txt'));
    }
}
