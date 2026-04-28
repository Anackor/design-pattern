<?php

namespace App\Domain\Order;

final readonly class OrderId
{
    private function __construct(
        private string $value
    ) {}

    public static function fromString(string $value): self
    {
        $normalized = trim($value);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Order id cannot be empty.');
        }

        if (preg_match('/\s/', $normalized)) {
            throw new \InvalidArgumentException('Order id cannot contain whitespace.');
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
