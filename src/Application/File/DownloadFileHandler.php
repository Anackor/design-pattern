<?php

namespace App\Application\File;

use App\Application\DTO\FileOperationRequestDTO;
use App\Infrastructure\Factory\FileStorageFactory;

class DownloadFileHandler
{
    public function __construct(private FileStorageFactory $factory) {}

    public function handle(FileOperationRequestDTO $dto): string
    {
        $adapter = $this->factory->create($dto->adapter);
        return $adapter->download($dto->path);
    }
}
