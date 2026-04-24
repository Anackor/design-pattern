<?php

namespace App\Application\Factory;

use App\Application\DTO\NotificationRequestDTO;
use App\Domain\Notification\NotificationInterface;

interface NotificationFactoryInterface
{
    public function create(NotificationRequestDTO $dto): NotificationInterface;
}
