<?php

namespace App\Domain\Order\State;

use App\Domain\Order\Order;
use App\Domain\Order\OrderStateInterface;

class PendingState implements OrderStateInterface
{
    private Order $order;

    public function setContext(Order $order): void
    {
        $this->order = $order;
    }

    public function pay(Order $order): void
    {
        $order->setState(new PaidState());
    }

    public function ship(Order $order): void
    {
        throw new \LogicException('Cannot ship an order that has not been paid.');
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
        return 'pending';
    }
}
