<?php

namespace App\Domain\Notification;

use App\Domain\Notification\NotificationInterface;

class WebhookNotification implements NotificationInterface
{
    public function send(string $title, string $receiver, string $message): bool
    {
        // Here, the real webhook sending logic would be implemented using an API.
        return true;
    }

    public function getChannelName(): string
    {
        return 'webhook';
    }
}
