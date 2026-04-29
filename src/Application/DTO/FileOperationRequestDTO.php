<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FileOperationRequestDTO
{
    #[Assert\NotBlank(message: 'Adapter cannot be empty.')]
    #[Assert\Choice(['aws', 's3', 'ftp', 'local'])]
    public string $adapter;

    #[Assert\NotBlank(message: 'Path cannot be empty.')]
    #[Assert\Type('string')]
    public string $path;

    #[Assert\Type('string')]
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
