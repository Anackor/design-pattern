<?php

namespace App\Application\Notification;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Factory\NotificationFactoryInterface;

class SendNotificationHandler
{
    private NotificationFactoryInterface $notificationFactory;

    public function __construct(NotificationFactoryInterface $notificationFactory)
    {
        $this->notificationFactory = $notificationFactory;
    }

    /**
     * Handle the sending of a notification.
     *
     * @param NotificationRequestDTO $notificationDTO
     */
    public function handle(NotificationRequestDTO $notificationDTO): void
    {
        $notification = $this->notificationFactory->create(
            $notificationDTO
        );

        $notification->send(
            $notificationDTO->getTitle(),
            $notificationDTO->getReceiver(),
            $notificationDTO->getMessage()
        );
    }
}
