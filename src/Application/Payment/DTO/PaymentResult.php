<?php

namespace App\Application\Payment\DTO;

/**
 * Represents the outcome of handling a request in the chain.
 * Used by handlers to signal success or failure,
 * and to provide additional context if needed.
 */
class PaymentResult
{
    private function __construct(
        private readonly bool $success,
        private readonly ?string $message = null
    ) {}

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(string $message): self
    {
        return new self(false, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
