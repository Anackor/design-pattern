<?php

namespace App\Domain\Notification;

use App\Domain\Notification\NotificationInterface;

class EmailNotification implements NotificationInterface
{
    public function send(string $title, string $receiver, string $message): bool
    {
        // Here, the real email sending logic would be implemented using an email service provider.
        return true;
    }

    public function getChannelName(): string
    {
        return 'email';
    }
}
