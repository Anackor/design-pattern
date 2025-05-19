<?php

namespace App\Domain\Flyweight;

class UserType
{
    public function __construct(
        private string $type
    ) {}

    public function getType(): string
    {
        return $this->type;
    }
}
