<?php

namespace App\Application\Logger;

use App\Domain\Logger\LoggerInterface;

/**
 * AbstractLoggerDecorator is the base class for all decorators.
 * 
 * It implements LoggerInterface and holds a reference to another LoggerInterface object.
 * Each decorator will extend this class to add functionality before/after the base log call.
 */
abstract class AbstractLoggerDecorator implements LoggerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log(string $message): void
    {
        // Pass-through to the wrapped logger; concrete decorators will override this
        $this->logger->log($message);
    }
}
