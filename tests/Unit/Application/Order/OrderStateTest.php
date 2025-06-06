<?php

namespace Tests\Domain\Order;

use App\Domain\Order\Order;
use App\Domain\Order\State\PendingState;
use PHPUnit\Framework\TestCase;

class OrderStateTest extends TestCase
{
    private function createOrder(string $stateClass = PendingState::class): Order
    {
        $state = new $stateClass();
        return new Order('order-1', $state);
    }

    public function testInitialStateIsPending(): void
    {
        $order = $this->createOrder();
        $this->assertSame('pending', $order->getState()->getStatus());
    }

    public function testPayFromPendingMovesToPaid(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $this->assertSame('paid', $order->getState()->getStatus());
    }

    public function testShipFromPaidMovesToShipped(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->ship();
        $this->assertSame('shipped', $order->getState()->getStatus());
    }

    public function testDeliverFromShippedMovesToDelivered(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->ship();
        $order->deliver();
        $this->assertSame('delivered', $order->getState()->getStatus());
    }

    public function testCancelFromPending(): void
    {
        $order = $this->createOrder();
        $order->cancel();
        $this->assertSame('cancelled', $order->getState()->getStatus());
    }

    public function testCancelFromPaid(): void
    {
        $order = $this->createOrder();
        $order->pay();
        $order->cancel();
        $this->assertSame('cancelled', $order->getState()->getStatus());
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

        $this->assertSame('delivered', $order->getState()->getStatus());

        $this->expectException(\LogicException::class);
        $order->cancel(); // Cannot cancel after delivery
    }

    public function testCannotTransitionFromCancelled(): void
    {
        $order = $this->createOrder();
        $order->cancel();

        $this->assertSame('cancelled', $order->getState()->getStatus());

        $this->expectException(\LogicException::class);
        $order->pay();
    }
}
