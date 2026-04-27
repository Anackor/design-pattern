<?php

namespace App\Domain\Order;

/**
 * Interface OrderStateInterface
 *
 * Represents a state in the State Design Pattern.
 * Each concrete implementation defines behavior for actions
 * that can be performed on an Order, based on its current state.
 *
 * Implementations of this interface should handle state-specific
 * logic and return the next valid state when appropriate.
 * This allows the Order context to change its behavior dynamically
 * without relying on conditional logic.
 */
interface OrderStateInterface
{
    public function pay(): OrderStateInterface;

    public function ship(): OrderStateInterface;

    public function deliver(): OrderStateInterface;

    public function cancel(): OrderStateInterface;

    public function getStatus(): OrderStatus;
}
