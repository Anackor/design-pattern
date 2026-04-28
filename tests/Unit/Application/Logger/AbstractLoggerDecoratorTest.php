<?php

namespace App\Tests\Unit\Application\Logger;

use App\Application\Logger\AbstractLoggerDecorator;
use App\Domain\Logger\LoggerInterface;
use PHPUnit\Framework\TestCase;

class AbstractLoggerDecoratorTest extends TestCase
{
    public function testBaseDecoratorPassesMessageThroughWrappedLogger(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('log')
            ->with('plain message');

        $decorator = new class ($logger) extends AbstractLoggerDecorator {};

        $decorator->log('plain message');
    }
}
