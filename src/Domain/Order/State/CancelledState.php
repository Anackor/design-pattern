<?php

namespace App\Domain\Order\State;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStateInterface;

class CancelledState implements OrderStateInterface
{
    public function setContext(Order $order): void
    {
        // Cancelled state doesn't require context
    }

    public function pay(Order $order): void
    {
        throw new \LogicException('Order is cancelled and cannot be paid.');
    }

    public function ship(Order $order): void
    {
        throw new \LogicException('Order is cancelled and cannot be shipped.');
    }

    public function deliver(Order $order): void
    {
        throw new \LogicException('Order is cancelled and cannot be delivered.');
    }

    public function cancel(Order $order): void
    {
        throw new \LogicException('Order is already cancelled.');
    }

    public function getStatus(): string
    {
        return 'cancelled';
    }
}
