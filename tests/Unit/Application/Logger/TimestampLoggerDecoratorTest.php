<?php

use PHPUnit\Framework\TestCase;
use App\Application\Logger\TimestampLoggerDecorator;
use App\Domain\Logger\LoggerInterface;

class TimestampLoggerDecoratorTest extends TestCase
{
    public function testAddsTimestampToLog(): void
    {
        $mock = $this->createMock(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('log')
            ->with($this->callback(function (string $message) {
                return preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] Test$/', $message) === 1;
            }));

        $decorator = new TimestampLoggerDecorator($mock);
        $decorator->log('Test');
    }
}
