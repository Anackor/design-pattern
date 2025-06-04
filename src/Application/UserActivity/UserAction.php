<?php

namespace App\Application\UserActivity;

readonly class UserAction
{
    public function __construct(
        public string $userId,
        public string $actionType,
        public \DateTimeImmutable $timestamp
    ) {}
}
