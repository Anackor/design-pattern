<?php

namespace App\Application\Factory;

use App\Application\DTO\NotificationRequestDTO;
use App\Domain\Enum\NotificationChannel;
use App\Domain\Notification\EmailNotification;
use App\Domain\Notification\NotificationInterface;
use App\Domain\Notification\SlackNotification;
use App\Domain\Notification\SmsNotification;
use App\Domain\Notification\WebhookNotification;

class NotificationFactory
{
    public function create(NotificationRequestDTO $dto): NotificationInterface
    {
        return match ($dto->getChannel()) {
            NotificationChannel::EMAIL => new EmailNotification(),
            NotificationChannel::SMS => new SmsNotification(),
            NotificationChannel::WEBHOOK => new WebhookNotification(),
            NotificationChannel::SLACK => new SlackNotification()
        };
    }
}
