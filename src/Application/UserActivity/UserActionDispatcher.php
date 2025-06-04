<?php

namespace App\Application\UserActivity;

class UserActionDispatcher
{
    public function __construct(private UserActionSubjectInterface $subject) {}

    public function recordAction(string $userId, string $actionType): void
    {
        $action = new UserAction($userId, $actionType, new \DateTimeImmutable());
        $this->subject->notify($action);
    }
}
