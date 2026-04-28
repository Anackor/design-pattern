<?php

namespace App\Application\Customer\Command;

final readonly class CustomerCommandResult
{
    public function __construct(
        private string $message
    ) {}

    public function message(): string
    {
        return $this->message;
    }
}
