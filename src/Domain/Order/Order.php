<?php

namespace App\Domain\Order;

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
    private string $id;
    private OrderStateInterface $state;

    public function __construct(string $id, OrderStateInterface $initialState)
    {
        $this->id = $id;
        $this->state = $initialState;
        $this->state->setContext($this);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setState(OrderStateInterface $state): void
    {
        $this->state = $state;
        $this->state->setContext($this);
    }

    public function getState(): OrderStateInterface
    {
        return $this->state;
    }

    public function pay(): void
    {
        $this->state->pay($this);
    }

    public function ship(): void
    {
        $this->state->ship($this);
    }

    public function deliver(): void
    {
        $this->state->deliver($this);
    }

    public function cancel(): void
    {
        $this->state->cancel($this);
    }
}
