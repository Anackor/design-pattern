<?php

namespace App\Domain\Flyweight;

class Country
{
    public function __construct(
        private string $name
    ) {}

    public function getName(): string
    {
        return $this->name;
    }
}
