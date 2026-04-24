<?php

namespace App\Application\Logger;

/**
 * TimestampLoggerDecorator adds a timestamp to each log message.
 *
 * It extends AbstractLoggerDecorator, allowing you to stack this decorator
 * with others to enrich the log output dynamically.
 */
class TimestampLoggerDecorator extends AbstractLoggerDecorator
{
    public function log(string $message): void
    {
        $timestampedMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        $this->logger->log($timestampedMessage);
    }
}
