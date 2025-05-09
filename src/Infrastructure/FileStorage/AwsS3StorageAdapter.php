<?php

namespace App\Infrastructure\FileStorage;

use App\Domain\Adapter\FileStorageInterface;
use App\Domain\Client\AwsS3ClientInterface;

class AwsS3StorageAdapter implements FileStorageInterface
{
    public function __construct(
        private AwsS3ClientInterface $client,
        private string $bucket
    ) {}

    public function upload(string $path, string $contents): bool
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $path,
            'Body'   => $contents,
        ]);
        return true;
    }

    public function download(string $path): string
    {
        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $path,
        ]);
        return (string) $result['Body'];
    }

    public function delete(string $path): bool
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $path,
        ]);
        return true;
    }
}
