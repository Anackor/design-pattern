<?php

namespace App\Application\Query;

class GetUserQuery
{
    public function __construct(public readonly int $userId) {}
}
