<?php

namespace App\Application\File;

use App\Application\DTO\FileOperationRequestDTO;

class UploadFileHandler
{
    public function __construct(private FileStorageResolverInterface $storageResolver) {}

    public function handle(FileOperationRequestDTO $dto): void
    {
        $adapter = $this->storageResolver->resolve($dto->adapter);
        $adapter->upload($dto->path, $dto->contents ?? '');
    }
}
