<?php

namespace App\Application\Customer\Command;

/**
 * Interface Command
 *
 * Defines the common structure for all Command classes. All commands should implement this interface
 * to ensure they have a consistent execute method.
 */
interface CommandInterface
{
    public function label(): string;

    public function execute(CustomerCommandPayload $payload): CustomerCommandResult;
}
