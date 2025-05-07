<?php

namespace App\Application\File;

use App\Application\DTO\FileOperationRequestDTO;
use App\Infrastructure\Factory\FileStorageFactory;

class DeleteFileHandler
{
    public function __construct(private FileStorageFactory $factory) {}

    public function handle(FileOperationRequestDTO $dto): void
    {
        $adapter = $this->factory->create($dto->adapter);
        $adapter->delete($dto->path);
    }
}
