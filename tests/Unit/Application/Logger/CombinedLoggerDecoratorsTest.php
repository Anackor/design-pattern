<?php

use App\Domain\Logger\LoggerInterface;
use PHPUnit\Framework\TestCase;
use App\Application\Logger\TimestampLoggerDecorator;
use App\Application\Logger\ErrorLevelLoggerDecorator;
use App\Application\Logger\UppercaseLoggerDecorator;

class CombinedLoggerDecoratorsTest extends TestCase
{
    public function testCombinesDecoratorsCorrectly(): void
    {
        $mock = $this->createMock(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('log')
            ->with($this->callback(function ($message) {
                return
                    str_starts_with($message, '[WARNING] [') &&
                    str_ends_with($message, 'TESTING') &&
                    preg_match('/\[WARNING\] \[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] TESTING/', $message) === 1;
            }));

        $logger = new TimestampLoggerDecorator(
            new ErrorLevelLoggerDecorator(
                new UppercaseLoggerDecorator($mock),
                'warning'
            )
        );

        $logger->log('Testing');
    }
}
