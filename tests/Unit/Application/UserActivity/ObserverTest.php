<?php

namespace App\Tests\Unit\Application\UserActivity;

use PHPUnit\Framework\TestCase;
use App\Application\UserActivity\UserActionSubject;
use App\Application\UserActivity\UserAction;
use App\Application\UserActivity\Observer\ActivityLogger;
use App\Application\UserActivity\Observer\UserMetricsTracker;
use App\Tests\Support\InMemoryLogger;

class ObserverTest extends TestCase
{
    public function testObserversReceiveUserAction(): void
    {
        $subject = new UserActionSubject();
        $loggerSpy = new InMemoryLogger();

        $logger = new ActivityLogger($loggerSpy);
        $tracker = new UserMetricsTracker();

        $subject->attach($logger);
        $subject->attach($tracker);

        $action = new UserAction('user-123', 'login', new \DateTimeImmutable());
        $subject->notify($action);

        $this->assertEquals(1, $tracker->getUserActionCount('user-123', 'login'));
        $this->assertCount(1, $loggerSpy->records);
        $this->assertSame('user.activity.recorded', $loggerSpy->records[0]['message']);
        $this->assertSame('observer', $loggerSpy->records[0]['context']['pattern']);
        $this->assertSame('user-123', $loggerSpy->records[0]['context']['user_id']);
    }

    public function testDetachPreventsNotification()
    {
        $subject = new UserActionSubject();
        $tracker = new UserMetricsTracker();

        $subject->attach($tracker);
        $subject->detach($tracker);

        $action = new UserAction('user-456', 'logout', new \DateTimeImmutable());
        $subject->notify($action);

        $this->assertEquals(0, $tracker->getUserActionCount('user-456', 'logout'));
    }

    public function testMultipleActionsAreTracked()
    {
        $tracker = new UserMetricsTracker();
        $subject = new UserActionSubject();
        $subject->attach($tracker);

        $actions = [
            new UserAction('user-789', 'view', new \DateTimeImmutable()),
            new UserAction('user-789', 'view', new \DateTimeImmutable()),
            new UserAction('user-789', 'purchase', new \DateTimeImmutable()),
        ];

        foreach ($actions as $action) {
            $subject->notify($action);
        }

        $this->assertEquals(2, $tracker->getUserActionCount('user-789', 'view'));
        $this->assertEquals(1, $tracker->getUserActionCount('user-789', 'purchase'));
    }
}
