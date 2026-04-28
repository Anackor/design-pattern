<?php

namespace App\Domain\Order\State;

use App\Domain\Order\OrderStateInterface;
use App\Domain\Order\OrderStatus;

class ShippedState implements OrderStateInterface
{
    public function pay(): OrderStateInterface
    {
        throw new \LogicException('Order is already paid and shipped.');
    }

    public function ship(): OrderStateInterface
    {
        throw new \LogicException('Order is already shipped.');
    }

    public function deliver(): OrderStateInterface
    {
        return new DeliveredState();
    }

    public function cancel(): OrderStateInterface
    {
        throw new \LogicException('Cannot cancel an order that has been shipped.');
    }

    public function getStatus(): OrderStatus
    {
        return OrderStatus::SHIPPED;
    }
}
