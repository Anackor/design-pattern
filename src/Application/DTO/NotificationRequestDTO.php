<?php

namespace App\Application\DTO;

use App\Domain\Enum\NotificationChannel;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class leverages Symfony's validation constraints to ensure data integrity before processing any logic
 *
 * That ensures not just the validation logic is centralized:
 * - The message parameter in each Assert rule allows us to define clear and user-friendly error outputs.
 * - These messages will appear in the API response when validation fails
 *
 * Makes APIs more predictable and easier to debug.
 */
class NotificationRequestDTO
{
    /**
     * @Assert\NotBlank(message="Title cannot be empty.")
     * @Assert\Length(
     *     max=54,
     *     maxMessage="Title cannot be longer than {{ limit }} characters."
     * )
     */
    private string $title;

    /**
     * @Assert\NotBlank(message="Message cannot be empty.")
     */
    private string $message;

    /**
     * @Assert\NotBlank(message="Receiver cannot be empty.")
     */
    private string $receiver;

    /**
     * The channel must be one of the predefined options.
     * We dynamically extract the allowed values using NotificationChannel::values().
     *
     * @Assert\Choice(
     *     callback={NotificationChannel::class, "values"},
     *     message="Invalid channel '{{ value }}'."
     * )
     */
    private string $channel;

    public function __construct(
        string $title,
        string $message,
        string $receiver,
        string $channel
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->receiver = $receiver;
        $this->channel = $channel;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}
