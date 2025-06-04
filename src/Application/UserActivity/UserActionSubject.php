<?php

namespace App\Application\UserActivity;

use App\Application\UserActivity\Observer\UserActionObserverInterface;
use SplObjectStorage;

/**
 * Implements the Subject part of the Observer pattern.
 * This class allows observers to subscribe to user actions and be notified when such actions occur.
 * 
 * Benefits of using the Observer pattern:
 * - Decouples the subject from its observers.
 * - Promotes extensibility: new observers can be added without modifying the subject.
 * - Facilitates cleaner and more modular event handling across the system.
 */
class UserActionSubject implements UserActionSubjectInterface
{
    /** @var SplObjectStorage<UserActionObserverInterface, null> */
    private SplObjectStorage $observers;

    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    public function attach(UserActionObserverInterface $observer): void
    {
        $this->observers->attach($observer);
    }

    public function detach(UserActionObserverInterface $observer): void
    {
        $this->observers->detach($observer);
    }

    public function notify(UserAction $action): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($action);
        }
    }
}
