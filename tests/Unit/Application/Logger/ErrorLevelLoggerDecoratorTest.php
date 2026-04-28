<?php

namespace App\Tests\Unit\Application\Logger;

use PHPUnit\Framework\TestCase;
use App\Application\Logger\ErrorLevelLoggerDecorator;
use App\Domain\Logger\LoggerInterface;

class ErrorLevelLoggerDecoratorTest extends TestCase
{
    public function testAddsLevelToLog(): void
    {
        $mock = $this->createMock(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('log')
            ->with('[ERROR] Something went wrong');

        $decorator = new ErrorLevelLoggerDecorator($mock, 'ERROR');
        $decorator->log('Something went wrong');
    }
}
