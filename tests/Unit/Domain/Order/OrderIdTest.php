<?php

namespace App\Tests\Unit\Domain\Order;

use App\Domain\Order\OrderId;
use PHPUnit\Framework\TestCase;

class OrderIdTest extends TestCase
{
    public function testFromStringNormalizesValidId(): void
    {
        $id = OrderId::fromString(' order-123 ');

        $this->assertSame('order-123', $id->value());
        $this->assertSame('order-123', (string) $id);
    }

    public function testFromStringRejectsEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order id cannot be empty.');

        OrderId::fromString('   ');
    }

    public function testFromStringRejectsWhitespaceInsideId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order id cannot contain whitespace.');

        OrderId::fromString('order 123');
    }

    public function testEqualsComparesValue(): void
    {
        $left = OrderId::fromString('order-123');
        $right = OrderId::fromString('order-123');

        $this->assertTrue($left->equals($right));
    }
}
