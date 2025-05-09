<?php

namespace App\Application\Service;

use App\Domain\Adapter\FileStorageInterface;

class FileStorageService
{
    public function __construct(private FileStorageInterface $storage) {}

    public function uploadFile(string $path, string $content): bool
    {
        return $this->storage->upload($path, $content);
    }

    public function downloadFile(string $path): string
    {
        return $this->storage->download($path);
    }

    public function deleteFile(string $path): bool
    {
        return $this->storage->delete($path);
    }
}
