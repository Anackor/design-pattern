<?php

namespace App\Application\UserActivity\Observer;

use App\Application\UserActivity\UserAction;

interface UserActionObserverInterface
{
    public function update(UserAction $action): void;
}
