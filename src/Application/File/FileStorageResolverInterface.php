<?php

namespace App\Application\File;

use App\Domain\Adapter\FileStorageInterface;

interface FileStorageResolverInterface
{
    public function resolve(string $type): FileStorageInterface;
}
