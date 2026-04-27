<?php

namespace App\Tests\Unit\Shared\ValueObject;

use App\Shared\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testFromFloatCreatesMoney(): void
    {
        $money = Money::fromFloat(12.99);

        $this->assertSame(1299, $money->amountInCents());
        $this->assertSame('EUR', $money->currency());
        $this->assertSame(12.99, $money->toFloat());
    }

    public function testAddSumsMatchingCurrencies(): void
    {
        $left = Money::fromFloat(12.50, 'EUR');
        $right = Money::fromFloat(7.50, 'EUR');

        $this->assertTrue($left->add($right)->equals(Money::fromFloat(20.00, 'EUR')));
    }

    public function testMultiplyAppliesFactor(): void
    {
        $money = Money::fromFloat(250.00);

        $this->assertTrue($money->multiply(0.85)->equals(Money::fromFloat(212.50)));
    }

    public function testFromDecimalStringCreatesMoneyWithoutFloatRounding(): void
    {
        $money = Money::fromDecimalString('12.50');

        $this->assertSame(1250, $money->amountInCents());
        $this->assertSame('12.50', $money->toDecimalString());
        $this->assertSame('12.5', $money->toDisplayString());
    }

    public function testFromFloatRejectsNegativeAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative.');

        Money::fromFloat(-1.0);
    }

    public function testAddRejectsDifferentCurrencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money currencies must match. Got EUR and USD.');

        Money::fromFloat(10.0, 'EUR')->add(Money::fromFloat(5.0, 'USD'));
    }

    public function testFromDecimalStringRejectsInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount must be a non-negative decimal with up to 2 decimals.');

        Money::fromDecimalString('12.345');
    }
}
