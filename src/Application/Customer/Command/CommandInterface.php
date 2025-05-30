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
    /**
     * Execute the command logic.
     *
     * This method will contain the business logic or interact with services to perform actions.
     */
    public function execute(): void;
}
