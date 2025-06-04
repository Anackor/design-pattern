<?php

namespace App\Application\Chat;

use App\Application\Chat\User;

interface ChatRoomMediatorInterface
{
    public function showMessage(User $user, string $message): void;
}
