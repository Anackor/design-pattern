<?php

namespace App\Domain\Order\State;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStateInterface;

class ShippedState implements OrderStateInterface
{
    public function setContext(Order $order): void
    {
        // Shipped state doesn't require context.
    }

    public function pay(Order $order): void
    {
        throw new \LogicException('Order is already paid and shipped.');
    }

    public function ship(Order $order): void
    {
        throw new \LogicException('Order is already shipped.');
    }

    public function deliver(Order $order): void
    {
        $order->setState(new DeliveredState());
    }

    public function cancel(Order $order): void
    {
        throw new \LogicException('Cannot cancel an order that has been shipped.');
    }

    public function getStatus(): string
    {
        return 'shipped';
    }
}
