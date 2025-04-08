<?php

namespace App\Domain\Enum;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case WEBHOOK = 'webhook';
    case SLACK = 'slack';

    public static function values(): array
    {
        return array_map(fn(self $channel) => $channel->value, self::cases());
    }
}
