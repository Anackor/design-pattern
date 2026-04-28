<?php

namespace App\Application\UserActivity;

/**
 * UserActionDispatcher is the small entry point that emits observable user actions.
 *
 * It does not perform logging directly. Instead, it creates a domain-level signal
 * and lets attached observers decide what to do with it. This separation is useful
 * for teaching because it keeps the action recording flow independent from metrics
 * or logging concerns.
 */
class UserActionDispatcher
{
    public function __construct(private UserActionSubjectInterface $subject) {}

    public function recordAction(string $userId, string $actionType): void
    {
        $action = new UserAction($userId, $actionType, new \DateTimeImmutable());
        $this->subject->notify($action);
    }
}
