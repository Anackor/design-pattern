<?php

namespace App\Application\Chat;

/**
 * This class represents a participant (or "colleague") in the Mediator design pattern.
 * It does not communicate directly with other colleagues; instead, it sends and receives
 * messages exclusively through the mediator.
 *
 * This promotes loose coupling between components and allows the mediator to control
 * how and when interactions happen.
 */
class User
{
    public function __construct(
        private readonly string $name,
        private readonly ChatRoomMediatorInterface $chatRoom
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function sendMessage(string $message): void
    {
        $this->chatRoom->showMessage($this, $message);
    }
}
