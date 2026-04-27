<?php

namespace App\Tests\Unit\Infrastructure\FileStorage;

use App\Infrastructure\Client\AwsS3ClientInterface;
use App\Infrastructure\FileStorage\AwsS3StorageAdapter;
use PHPUnit\Framework\TestCase;
use Aws\Result;

class AwsS3StorageAdapterTest extends TestCase
{
    public function testUploadCallsPutObject(): void
    {
        $mockClient = $this->createMock(AwsS3ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('putObject')
            ->with([
                'Bucket' => 'my-bucket',
                'Key'    => 'file.txt',
                'Body'   => 'hello world',
            ]);

        $adapter = new AwsS3StorageAdapter($mockClient, 'my-bucket');
        $result = $adapter->upload('file.txt', 'hello world');

        $this->assertTrue($result);
    }

    public function testDownloadCallsGetObject(): void
    {
        $mockClient = $this->createMock(AwsS3ClientInterface::class);
        $mockResult = new Result(['Body' => 'file content']);

        $mockClient->expects($this->once())
            ->method('getObject')
            ->with([
                'Bucket' => 'my-bucket',
                'Key'    => 'file.txt',
            ])
            ->willReturn($mockResult);

        $adapter = new AwsS3StorageAdapter($mockClient, 'my-bucket');
        $result = $adapter->download('file.txt');

        $this->assertEquals('file content', $result);
    }

    public function testDeleteCallsDeleteObject(): void
    {
        $mockClient = $this->createMock(AwsS3ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('deleteObject')
            ->with([
                'Bucket' => 'my-bucket',
                'Key'    => 'file.txt',
            ]);

        $adapter = new AwsS3StorageAdapter($mockClient, 'my-bucket');
        $result = $adapter->delete('file.txt');

        $this->assertTrue($result);
    }
}
