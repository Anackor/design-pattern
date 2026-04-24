<?php

namespace App\Domain\Order\State;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStateInterface;

class PaidState implements OrderStateInterface
{
    public function setContext(Order $order): void
    {
        // Paid state doesn't require context.
    }

    public function pay(Order $order): void
    {
        throw new \LogicException('Order is already paid.');
    }

    public function ship(Order $order): void
    {
        $order->setState(new ShippedState());
    }

    public function deliver(Order $order): void
    {
        throw new \LogicException('Cannot deliver an order that has not been shipped.');
    }

    public function cancel(Order $order): void
    {
        $order->setState(new CancelledState());
    }

    public function getStatus(): string
    {
        return 'paid';
    }
}
