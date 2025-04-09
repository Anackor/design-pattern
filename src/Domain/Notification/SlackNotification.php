<?php

namespace App\Domain\Notification;

use App\Domain\Notification\NotificationInterface;

class SlackNotification implements NotificationInterface
{
    public function send(string $title, string $receiver, string $message): bool
    {
        // Here, the real Slack sending logic would be implemented using Slack's API.
        return true;
    }

    public function getChannelName(): string
    {
        return 'slack';
    }
}
