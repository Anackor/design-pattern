<?php

use PHPUnit\Framework\TestCase;
use App\Application\Logger\UppercaseLoggerDecorator;
use App\Domain\Logger\LoggerInterface;

class UppercaseLoggerDecoratorTest extends TestCase
{
    public function testConvertsMessageToUppercase(): void
    {
        $mock = $this->createMock(LoggerInterface::class);
        $mock->expects($this->once())
            ->method('log')
            ->with('HELLO WORLD');

        $decorator = new UppercaseLoggerDecorator($mock);
        $decorator->log('Hello World');
    }
}
