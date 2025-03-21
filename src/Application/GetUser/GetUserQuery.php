<?php

namespace App\Application\GetUser;

class GetUserQuery
{
    public function __construct(public readonly int $userId) {}
}
