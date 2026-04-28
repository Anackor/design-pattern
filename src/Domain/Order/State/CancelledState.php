<?php

namespace App\Domain\Order\State;

use App\Domain\Order\OrderStateInterface;
use App\Domain\Order\OrderStatus;

class CancelledState implements OrderStateInterface
{
    public function pay(): OrderStateInterface
    {
        throw new \LogicException('Order is cancelled and cannot be paid.');
    }

    public function ship(): OrderStateInterface
    {
        throw new \LogicException('Order is cancelled and cannot be shipped.');
    }

    public function deliver(): OrderStateInterface
    {
        throw new \LogicException('Order is cancelled and cannot be delivered.');
    }

    public function cancel(): OrderStateInterface
    {
        throw new \LogicException('Order is already cancelled.');
    }

    public function getStatus(): OrderStatus
    {
        return OrderStatus::CANCELLED;
    }
}
