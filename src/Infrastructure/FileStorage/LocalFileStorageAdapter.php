<?php

namespace App\Infrastructure\FileStorage;

use App\Domain\Adapter\FileStorageInterface;

class LocalFileStorageAdapter implements FileStorageInterface
{
    public function __construct(private string $basePath) {}

    public function upload(string $path, string $contents): bool
    {
        return file_put_contents($this->basePath . '/' . $path, $contents) !== false;
    }

    public function download(string $path): string
    {
        return file_get_contents($this->basePath . '/' . $path);
    }

    public function delete(string $path): bool
    {
        return unlink($this->basePath . '/' . $path);
    }
}
