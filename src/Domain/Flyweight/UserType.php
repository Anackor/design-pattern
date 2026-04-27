<?php

namespace App\Domain\Flyweight;

final readonly class UserType
{
    private function __construct(
        private string $type
    ) {}

    public static function fromString(string $type): self
    {
        return new self(self::normalize($type));
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function equals(self $other): bool
    {
        return $this->type === $other->type;
    }

    private static function normalize(string $type): string
    {
        $normalized = trim($type);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('User type cannot be empty.');
        }

        return $normalized;
    }
}
