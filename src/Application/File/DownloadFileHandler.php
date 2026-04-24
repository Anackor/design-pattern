<?php

namespace App\Application\File;

use App\Application\DTO\FileOperationRequestDTO;

class DownloadFileHandler
{
    public function __construct(private FileStorageResolverInterface $storageResolver) {}

    public function handle(FileOperationRequestDTO $dto): string
    {
        $adapter = $this->storageResolver->resolve($dto->adapter);
        return $adapter->download($dto->path);
    }
}
