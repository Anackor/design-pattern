<?php

namespace App\Domain\Order\State;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStateInterface;

class DeliveredState implements OrderStateInterface
{
    public function setContext(Order $order): void
    {
        // Delivered state doesn't require context
    }

    public function pay(Order $order): void
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function ship(Order $order): void
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function deliver(Order $order): void
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function cancel(Order $order): void
    {
        throw new \LogicException('Cannot cancel a delivered order.');
    }

    public function getStatus(): string
    {
        return 'delivered';
    }
}
