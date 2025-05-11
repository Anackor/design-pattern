<?php

namespace App\Domain\Logger;

/**
 * LoggerInterface defines the contract for all loggers.
 * 
 * In the Decorator pattern, this interface allows both base loggers
 * and decorators to be used interchangeably, enabling dynamic behavior extension.
 */
interface LoggerInterface
{
    public function log(string $message): void;
}
