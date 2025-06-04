<?php

namespace App\Application\UserActivity\Observer;

use App\Application\UserActivity\UserAction;
use Psr\Log\LoggerInterface;

class ActivityLogger implements UserActionObserverInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function update(UserAction $action): void
    {
        $this->logger->info(sprintf(
            '[%s] User %s performed %s',
            $action->timestamp->format('Y-m-d H:i:s'),
            $action->userId,
            $action->actionType
        ));
    }
}
