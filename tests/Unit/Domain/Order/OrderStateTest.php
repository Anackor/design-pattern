<?php

namespace App\Tests\Unit\Domain\Order;

use App\Domain\Order\Order;
use App\Domain\Order\OrderId;
use App\Domain\Order\OrderStatus;
use App\Domain\Order\State\PendingState;
use App\Domain\Order\State\PaidState;
use PHPUnit\Framework\TestCase;

class OrderStateTest extends TestCase
{
    private function createOrder(string $stateClass = PendingState::class): Order
    {
        $state = new $stateClass();

        return Order::place('order-1', $state);
    }

    public function testPlaceCreatesPendingOrderByDefault(): void
    {
        $order = Order::place('order-1');

        $this->assertSame('order-1', $order->getId());
        $this->assertTrue($order->getOrderId()->equals(OrderId::fromString('order-1')));
        $this->assertSame(OrderStatus::PENDING, $order->getStatus());
    }

    public function testInitialStateIsPending(): void
    {
        $order = $this->createOrder();

        $this->assertSame(OrderStatus::PENDING, $order->getStatus());
    }

    public function testPayFromPendingMovesToPaid(): void
    {
        $order = $this->createOrder();
        $order->pay();

        $this->assertSame(OrderStatus::PAID, $order->getStatus());
    }

    public function testShipFromPaidMovesToShipped(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->ship();

        $this->assertSame(OrderStatus::SHIPPED, $order->getStatus());
    }

    public function testDeliverFromShippedMovesToDelivered(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->ship();
        $order->deliver();

        $this->assertSame(OrderStatus::DELIVERED, $order->getStatus());
    }

    public function testCancelFromPending(): void
    {
        $order = $this->createOrder();
        $order->cancel();

        $this->assertSame(OrderStatus::CANCELLED, $order->getStatus());
    }

    public function testCancelFromPaid(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->cancel();

        $this->assertSame(OrderStatus::CANCELLED, $order->getStatus());
    }

    public function testCannotCancelFromShipped(): void
    {
        $this->expectException(\LogicException::class);
        $order = $this->createOrder();
        $order->pay();
        $order->ship();
        $order->cancel();
    }

    public function testCannotShipBeforePaying(): void
    {
        $this->expectException(\LogicException::class);
        $order = $this->createOrder();
        $order->ship();
    }

    public function testCannotDeliverBeforeShipping(): void
    {
        $this->expectException(\LogicException::class);
        $order = $this->createOrder();
        $order->pay();
        $order->deliver();
    }

    public function testCannotTransitionFromDelivered(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->ship();
        $order->deliver();

        $this->assertSame(OrderStatus::DELIVERED, $order->getStatus());

        $this->expectException(\LogicException::class);
        $order->cancel(); // Cannot cancel after delivery
    }

    public function testCannotTransitionFromCancelled(): void
    {
        $order = $this->createOrder();
        $order->cancel();

        $this->assertSame(OrderStatus::CANCELLED, $order->getStatus());

        $this->expectException(\LogicException::class);
        $order->pay();
    }

    public function testPlaceRejectsEmptyOrderId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order id cannot be empty.');

        Order::place('   ');
    }

    public function testCanStartFromSpecificStateForPatternExample(): void
    {
        $order = Order::place('order-1', new PaidState());

        $this->assertSame(OrderStatus::PAID, $order->getStatus());
    }
}
