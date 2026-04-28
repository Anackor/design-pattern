<?php

namespace App\Tests\Unit\Infrastructure\FileStorage;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Client\FtpClient;
use App\Infrastructure\FileStorage\FtpStorageAdapter;

class FtpStorageAdapterTest extends TestCase
{
    private $ftpClientMock;
    private FtpStorageAdapter $adapter;

    protected function setUp(): void
    {
        $this->ftpClientMock = $this->createMock(FtpClient::class);
        $this->adapter = new FtpStorageAdapter($this->ftpClientMock);
    }

    public function testUploadCallsPut(): void
    {
        $this->ftpClientMock->expects($this->once())
            ->method('put')
            ->with('remote.txt', $this->callback('is_string')) // temp file path
            ->willReturn(true);

        $this->assertTrue($this->adapter->upload('remote.txt', 'content'));
    }

    public function testDownloadCallsGet(): void
    {
        $this->ftpClientMock->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($remote, $local) {
                file_put_contents($local, 'downloaded content');
                return true;
            });

        $result = $this->adapter->download('remote.txt');
        $this->assertSame('downloaded content', $result);
    }

    public function testDeleteCallsDelete(): void
    {
        $this->ftpClientMock->expects($this->once())
            ->method('delete')
            ->with('remote.txt')
            ->willReturn(true);

        $this->assertTrue($this->adapter->delete('remote.txt'));
    }
}
