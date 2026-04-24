<?php

namespace App\Application\UserActivity\Observer;

use App\Application\UserActivity\UserAction;

class UserMetricsTracker implements UserActionObserverInterface
{
    private array $actionCounts = [];

    public function update(UserAction $action): void
    {
        $this->actionCounts[$action->userId][$action->actionType] =
            ($this->actionCounts[$action->userId][$action->actionType] ?? 0) + 1;
    }

    public function getUserActionCount(string $userId, string $actionType): int
    {
        return $this->actionCounts[$userId][$actionType] ?? 0;
    }
}
