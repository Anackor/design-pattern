<?php

namespace App\Shared\ValueObject;

final readonly class Email
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        $normalized = trim($value);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Email cannot be empty.');
        }

        if (false === filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid email address: %s', $value));
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
