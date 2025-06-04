<?php

namespace App\Application\UserActivity;

use App\Application\UserActivity\Observer\UserActionObserverInterface;

interface UserActionSubjectInterface
{
    public function attach(UserActionObserverInterface $observer): void;
    public function detach(UserActionObserverInterface $observer): void;
    public function notify(UserAction $action): void;
}
