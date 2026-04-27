<?php

namespace App\Application\Customer\Command;

final readonly class CustomerCommandPayload
{
    private function __construct(
        private ?int $customerId = null,
        private ?string $name = null,
        private ?string $email = null
    ) {}

    public static function empty(): self
    {
        return new self();
    }

    public static function create(string $name, string $email): self
    {
        return new self(name: $name, email: $email);
    }

    public static function update(int $customerId, string $name, string $email): self
    {
        return new self($customerId, $name, $email);
    }

    public static function delete(int $customerId): self
    {
        return new self($customerId);
    }

    public function customerId(): int
    {
        if (null === $this->customerId) {
            throw new \LogicException('Customer command requires a customer id.');
        }

        return $this->customerId;
    }

    public function name(): string
    {
        if (null === $this->name || '' === trim($this->name)) {
            throw new \LogicException('Customer command requires a customer name.');
        }

        return trim($this->name);
    }

    public function email(): string
    {
        if (null === $this->email || '' === trim($this->email)) {
            throw new \LogicException('Customer command requires a customer email.');
        }

        return trim($this->email);
    }
}
