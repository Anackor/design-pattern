<?php

namespace App\Application\Logger;

use App\Domain\Logger\LoggerInterface;

/**
 * ErrorLevelLoggerDecorator adds a severity level to the log message.
 * 
 * This is useful for distinguishing between INFO, WARNING, ERROR, etc.
 * Can be combined with other decorators for richer output.
 */
class ErrorLevelLoggerDecorator extends AbstractLoggerDecorator
{
    private string $level;

    public function __construct(LoggerInterface $logger, string $level = 'INFO')
    {
        parent::__construct($logger);
        $this->level = strtoupper($level);
    }

    public function log(string $message): void
    {
        $levelledMessage = '[' . $this->level . '] ' . $message;
        $this->logger->log($levelledMessage);
    }
}
