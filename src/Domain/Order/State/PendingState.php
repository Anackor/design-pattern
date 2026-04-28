<?php

namespace App\Domain\Order\State;

use App\Domain\Order\OrderStateInterface;
use App\Domain\Order\OrderStatus;

class PendingState implements OrderStateInterface
{
    public function pay(): OrderStateInterface
    {
        return new PaidState();
    }

    public function ship(): OrderStateInterface
    {
        throw new \LogicException('Cannot ship an order that has not been paid.');
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
        return OrderStatus::PENDING;
    }
}
