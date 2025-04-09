<?php

namespace App\Application\Factory;

use App\Application\DTO\NotificationRequestDTO;
use App\Domain\Enum\NotificationChannel;
use App\Domain\Notification\EmailNotification;
use App\Domain\Notification\NotificationInterface;
use App\Domain\Notification\SlackNotification;
use App\Domain\Notification\SmsNotification;
use App\Domain\Notification\WebhookNotification;

/**
 * This factory is responsible for creating instances of different types of notifications based on the provided input.
 * This pattern encapsulates the object creation logic, promoting loose coupling and making the system easier to extend in the future
 * adding new notification types without modifying existing code.
 * 
 * It also provides better maintainability by centralizing the creation process in a single location,
 * reducing the risk of redundant or inconsistent object creation logic across the application.
 */
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
