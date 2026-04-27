<?php

namespace App\Domain\Order\State;

use App\Domain\Order\OrderStateInterface;
use App\Domain\Order\OrderStatus;

class PaidState implements OrderStateInterface
{
    public function pay(): OrderStateInterface
    {
        throw new \LogicException('Order is already paid.');
    }

    public function ship(): OrderStateInterface
    {
        return new ShippedState();
    }

    public function deliver(): OrderStateInterface
    {
        throw new \LogicException('Cannot deliver an order that has not been shipped.');
    }

    public function cancel(): OrderStateInterface
    {
        return new CancelledState();
    }

    public function getStatus(): OrderStatus
    {
        return OrderStatus::PAID;
    }
}
