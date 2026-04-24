<?php

namespace App\Infrastructure\FileStorage;

use App\Application\File\FileStorageResolverInterface;
use App\Domain\Adapter\FileStorageInterface;

class FileStorageResolver implements FileStorageResolverInterface
{
    public function __construct(
        private LocalFileStorageAdapter $localStorage,
        private AwsS3StorageAdapter $awsStorage,
        private FtpStorageAdapter $ftpStorage
    ) {}

    public function resolve(string $type): FileStorageInterface
    {
        return match ($type) {
            'local' => $this->localStorage,
            'aws', 's3' => $this->awsStorage,
            'ftp' => $this->ftpStorage,
            default => throw new \InvalidArgumentException("Unsupported storage type: $type"),
        };
    }
}
