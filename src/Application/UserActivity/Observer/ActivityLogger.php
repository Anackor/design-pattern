<?php

namespace App\Application\UserActivity\Observer;

use App\Application\UserActivity\UserAction;
use Psr\Log\LoggerInterface;

/**
 * ActivityLogger shows the Observer pattern in a form that is also operationally useful.
 *
 * The dispatcher emits a user action, and this observer turns that signal into a
 * structured log record without changing the main flow. This is a good teaching
 * example because it demonstrates observability as a side effect, not as a reason
 * to contaminate the domain action itself.
 */
class ActivityLogger implements UserActionObserverInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function update(UserAction $action): void
    {
        $this->logger->info('user.activity.recorded', [
            'pattern' => 'observer',
            'observer' => self::class,
            'user_id' => $action->userId,
            'action_type' => $action->actionType,
            'occurred_at' => $action->timestamp,
        ]);
    }
}
