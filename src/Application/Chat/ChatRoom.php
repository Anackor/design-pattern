<?php

namespace App\Application\Chat;

use App\Application\Chat\User;

/**
 * The Mediator design pattern defines an object (the Mediator) that encapsulates how a set of objects interact.
 * This pattern promotes loose coupling by preventing objects from referring to each other explicitly,
 * and it allows the interaction between them to be centralized in one place.
 * Instead of components communicating directly with each other and forming tight interdependencies,
 * they send messages or requests through the Mediator, which then coordinates the communication.
 * The main benefits of the Mediator pattern include:
 * Reducing the complexity of communication between many objects.
 * Encouraging separation of concerns.
 * Making it easier to add or modify interactions without changing the components themselves.
 * This implementation represents the central coordinating component that receives and dispatches messages
 * among participants, acting as the sole channel of communication.
*/
class ChatRoom implements ChatRoomMediatorInterface
{
    public function showMessage(User $user, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[$timestamp] {$user->getName()}: $message\n";
    }
}
