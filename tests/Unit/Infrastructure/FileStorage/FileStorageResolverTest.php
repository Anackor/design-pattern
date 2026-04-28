<?php

namespace App\Tests\Unit\Infrastructure\FileStorage;

use App\Domain\Adapter\FileStorageInterface;
use App\Infrastructure\FileStorage\AwsS3StorageAdapter;
use App\Infrastructure\FileStorage\FileStorageResolver;
use App\Infrastructure\FileStorage\FtpStorageAdapter;
use App\Infrastructure\FileStorage\LocalFileStorageAdapter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileStorageResolverTest extends TestCase
{
    private LocalFileStorageAdapter $localStorage;
    private AwsS3StorageAdapter $awsStorage;
    private FtpStorageAdapter $ftpStorage;
    private FileStorageResolver $resolver;

    protected function setUp(): void
    {
        $this->localStorage = $this->createMock(LocalFileStorageAdapter::class);
        $this->awsStorage = $this->createMock(AwsS3StorageAdapter::class);
        $this->ftpStorage = $this->createMock(FtpStorageAdapter::class);
        $this->resolver = new FileStorageResolver($this->localStorage, $this->awsStorage, $this->ftpStorage);
    }

    public static function supportedTypeProvider(): array
    {
        return [
            'local' => ['local', 'localStorage'],
            'aws' => ['aws', 'awsStorage'],
            's3' => ['s3', 'awsStorage'],
            'ftp' => ['ftp', 'ftpStorage'],
        ];
    }

    #[DataProvider('supportedTypeProvider')]
    public function testResolveReturnsExpectedAdapter(string $type, string $expectedProperty): void
    {
        $resolved = $this->resolver->resolve($type);

        $this->assertInstanceOf(FileStorageInterface::class, $resolved);
        $this->assertSame($this->{$expectedProperty}, $resolved);
    }

    public function testResolveRejectsUnsupportedType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported storage type: azure');

        $this->resolver->resolve('azure');
    }
}
