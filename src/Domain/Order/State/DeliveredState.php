<?php

namespace App\Domain\Order\State;

use App\Domain\Order\OrderStateInterface;
use App\Domain\Order\OrderStatus;

class DeliveredState implements OrderStateInterface
{
    public function pay(): OrderStateInterface
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function ship(): OrderStateInterface
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function deliver(): OrderStateInterface
    {
        throw new \LogicException('Order is already delivered.');
    }

    public function cancel(): OrderStateInterface
    {
        throw new \LogicException('Cannot cancel a delivered order.');
    }

    public function getStatus(): OrderStatus
    {
        return OrderStatus::DELIVERED;
    }
}
