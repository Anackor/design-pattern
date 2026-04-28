<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Entity\UserOrders;
use App\Shared\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class UserOrdersTest extends TestCase
{
    public function testPlaceForCreatesValidOrder(): void
    {
        $user = User::register('Jane Doe', 'jane@example.com');
        $createdAt = new \DateTimeImmutable('2026-01-15 10:30:00');

        $order = UserOrders::placeFor($user, Money::fromDecimalString('19.99'), $createdAt);

        $this->assertSame($user, $order->getUser());
        $this->assertSame('19.99', $order->getTotalPrice());
        $this->assertTrue($order->getTotalPriceMoney()->equals(Money::fromDecimalString('19.99')));
        $this->assertSame($createdAt, $order->getCreatedAt());
    }

    public function testSetTotalPriceNormalizesDecimalString(): void
    {
        $order = UserOrders::placeFor(
            User::register('Jane Doe', 'jane@example.com'),
            Money::fromDecimalString('10.00')
        );

        $order->setTotalPrice('12.5');

        $this->assertSame('12.50', $order->getTotalPrice());
        $this->assertSame(1250, $order->getTotalPriceMoney()->amountInCents());
    }

    public function testGetTotalPriceFailsWhenOrderIsIncomplete(): void
    {
        $order = new UserOrders();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User order total price has not been initialized.');

        $order->getTotalPrice();
    }

    public function testSetTotalPriceRejectsInvalidDecimalFormat(): void
    {
        $order = UserOrders::placeFor(
            User::register('Jane Doe', 'jane@example.com'),
            Money::fromDecimalString('10.00')
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount must be a non-negative decimal with up to 2 decimals.');

        $order->setTotalPrice('12.345');
    }

    public function testSetCreatedAtRejectsFutureDates(): void
    {
        $order = new UserOrders();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User order creation date cannot be in the future.');

        $order->setCreatedAt(new \DateTimeImmutable('+1 day'));
    }
}
