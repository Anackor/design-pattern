<?php

namespace App\Application\Notification;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Factory\NotificationFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * SendNotificationHandler is one of the clearest places to teach observability:
 * it sits on an outbound boundary where the application chooses a channel and
 * attempts to talk to the outside world.
 *
 * The handler logs only operationally useful metadata:
 * - which channel was requested;
 * - which concrete notification implementation was resolved;
 * - whether the send succeeded, returned false or failed with an exception.
 *
 * It deliberately avoids logging the full receiver or message body so that the
 * example also demonstrates data minimization as part of logging best practices.
 */
class SendNotificationHandler
{
    public function __construct(
        private NotificationFactoryInterface $notificationFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * Handle the sending of a notification.
     *
     * @param NotificationRequestDTO $notificationDTO
     */
    public function handle(NotificationRequestDTO $notificationDTO): void
    {
        $context = [
            'use_case' => self::class,
            'requested_channel' => $notificationDTO->getChannel(),
            'receiver_preview' => $this->maskReceiver($notificationDTO->getReceiver()),
            'message_length' => strlen($notificationDTO->getMessage()),
        ];

        // We log before leaving the application boundary so failed attempts are observable too.
        $this->logger->info('notification.send.started', $context);

        try {
            $notification = $this->notificationFactory->create($notificationDTO);
            $sent = $notification->send(
                $notificationDTO->getTitle(),
                $notificationDTO->getReceiver(),
                $notificationDTO->getMessage()
            );

            $resultContext = $context + [
                'resolved_channel' => $notification->getChannelName(),
                'notification_class' => $notification::class,
            ];

            if ($sent) {
                $this->logger->info('notification.send.succeeded', $resultContext);

                return;
            }

            $this->logger->warning('notification.send.returned_false', $resultContext);
        } catch (\Throwable $exception) {
            $this->logger->error('notification.send.failed', $context + ['exception' => $exception]);

            throw $exception;
        }
    }

    private function maskReceiver(string $receiver): string
    {
        if (str_contains($receiver, '@')) {
            [$localPart, $domain] = explode('@', $receiver, 2);

            return substr($localPart, 0, 1) . '***@' . $domain;
        }

        if (strlen($receiver) <= 4) {
            return '***';
        }

        return substr($receiver, 0, 2) . str_repeat('*', max(strlen($receiver) - 4, 1)) . substr($receiver, -2);
    }
}
