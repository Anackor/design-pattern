<?php

namespace App\Domain\Flyweight;

final readonly class Country
{
    private function __construct(
        private string $name
    ) {}

    public static function fromName(string $name): self
    {
        return new self(self::normalize($name));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name;
    }

    private static function normalize(string $name): string
    {
        $normalized = trim($name);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Country name cannot be empty.');
        }

        return $normalized;
    }
}
