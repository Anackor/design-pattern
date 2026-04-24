<?php

namespace App\Infrastructure\Factory;

use App\Domain\Adapter\FileStorageInterface;
use App\Infrastructure\Client\AwsS3Client;
use App\Infrastructure\FileStorage\FtpStorageAdapter;
use App\Infrastructure\FileStorage\AwsS3StorageAdapter;
use App\Infrastructure\Client\FtpClient;
use App\Infrastructure\FileStorage\LocalFileStorageAdapter;

/**
 * This factory encapsulates the logic for creating the appropriate FileStorageAdapter
 * based on configuration or runtime input.
 *
 * The factory pattern works in harmony with the adapter pattern here:
 * - The factory abstracts the instantiation process, hiding the complexity and dependencies
 *   (e.g., credentials or custom clients).
 * - The adapters ensure that, once instantiated, all storage mechanisms expose the same behavior
 *   through the FileStorageInterface.
 *
 * Together, they provide a clean, extensible, and testable architecture that isolates
 * infrastructure concerns from application logic.
 */
class FileStorageFactory
{
    public function create(string $type): FileStorageInterface
    {
        return match ($type) {
            'local' => new LocalFileStorageAdapter($_ENV['LOCAL_STORAGE_PATH']),
            'aws', 's3' => new AwsS3StorageAdapter(
                new AwsS3Client(
                    $_ENV['AWS_ACCESS_KEY_ID'],
                    $_ENV['AWS_SECRET_ACCESS_KEY'],
                    $_ENV['AWS_REGION']
                ),
                $_ENV['AWS_BUCKET']
            ),
            'ftp'   => new FtpStorageAdapter(
                new FtpClient(
                    host: $_ENV['FTP_HOST'],
                    user: $_ENV['FTP_USER'],
                    password: $_ENV['FTP_PASSWORD']
                )
            ),
            default => throw new \InvalidArgumentException("Unsupported storage type: $type")
        };
    }
}
