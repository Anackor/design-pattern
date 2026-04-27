<?php

namespace App\Domain\Notification;

/**
 * This interface defines the structure that all notification channels must follow to send notifications
 *
 */
interface NotificationInterface
{
    /**
     * Sends the notification message to the receiver
     *
     * @param string $title The title of the message.
     * @param string $receiver The receiver's identifier.
     * @param string $message The message to be sent.
     * @return bool True if the message was sent successfully, false otherwise.
     */
    public function send(string $title, string $receiver, string $message): bool;

    /**
     * Gets the name of the notification channel
     *
     * @return string The name of the channel.
     */
    public function getChannelName(): string;
}
