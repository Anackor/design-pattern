<?php

namespace App\Domain\Notification;

use App\Domain\Notification\NotificationInterface;

class SmsNotification implements NotificationInterface
{
    public function send(string $title, string $receiver, string $message): bool
    {
        // Here, the real sms sending logic would be implemented using an SMS API.
        return true;
    }

    public function getChannelName(): string
    {
        return 'sms';
    }
}
