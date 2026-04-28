<?php

namespace App\Domain\Order;

use App\Domain\Order\State\PendingState;

/**
 * Class Order
 *
 * This class represents the Context in the State Design Pattern.
 * It maintains a reference to the current state (OrderStateInterface)
 * and delegates state-specific behavior to the current state object.
 *
 * The Order class exposes domain actions (e.g., pay, ship, deliver, cancel),
 * but the behavior of each action depends entirely on the current state.
 * State transitions are handled internally by the state objects themselves,
 * allowing the Order to change its behavior at runtime without relying on conditionals.
 *
 * This design promotes encapsulation and adheres to the Open/Closed Principle,
 * making it easier to add or modify states without altering the Order class.
 */
class Order
{
    private OrderId $id;
    private OrderStateInterface $state;

    private function __construct(OrderId $id, OrderStateInterface $initialState)
    {
        $this->id = $id;
        $this->state = $initialState;
    }

    public static function place(string|OrderId $id, ?OrderStateInterface $initialState = null): self
    {
        $orderId = $id instanceof OrderId ? $id : OrderId::fromString($id);

        return new self($orderId, $initialState ?? new PendingState());
    }

    public function getId(): string
    {
        return $this->id->value();
    }

    public function getOrderId(): OrderId
    {
        return $this->id;
    }

    public function getState(): OrderStateInterface
    {
        return $this->state;
    }

    public function getStatus(): OrderStatus
    {
        return $this->state->getStatus();
    }

    public function pay(): void
    {
        $this->transitionTo($this->state->pay());
    }

    public function ship(): void
    {
        $this->transitionTo($this->state->ship());
    }

    public function deliver(): void
    {
        $this->transitionTo($this->state->deliver());
    }

    public function cancel(): void
    {
        $this->transitionTo($this->state->cancel());
    }

    private function transitionTo(OrderStateInterface $state): void
    {
        $this->state = $state;
    }
}
