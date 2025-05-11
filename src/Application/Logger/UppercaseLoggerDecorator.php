<?php

namespace App\Application\Logger;

/**
 * UppercaseLoggerDecorator transforms the log message to uppercase.
 * 
 * This example demonstrates how you can easily plug in new behavior
 * using the Decorator pattern without altering existing loggers.
 */
class UppercaseLoggerDecorator extends AbstractLoggerDecorator
{
    public function log(string $message): void
    {
        $uppercasedMessage = strtoupper($message);
        $this->logger->log($uppercasedMessage);
    }
}
