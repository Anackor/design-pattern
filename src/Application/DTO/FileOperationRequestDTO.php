<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FileOperationRequestDTO
{
    #[Assert\Choice(['aws', 'ftp', 'local'])]
    public string $adapter;

    #[Assert\Type("string")]
    public string $path;

    #[Assert\Type("string")]
    public ?string $contents = null;

    public function __construct(
        string $adapter,
        string $path,
        ?string $contents = null
    ) {
        $this->adapter = $adapter;
        $this->path = $path;
        $this->contents = $contents;
    }
}
