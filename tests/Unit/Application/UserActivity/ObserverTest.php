<?php

namespace App\Tests\Application\tion\UserActivity;

use PHPUnit\Framework\TestCase;
use App\Application\UserActivity\UserActionSubject;
use App\Application\UserActivity\UserAction;
use App\Application\UserActivity\Observer\ActivityLogger;
use App\Application\UserActivity\Observer\UserMetricsTracker;
use Psr\Log\LoggerInterface;

class ObserverTest extends TestCase
{
    public function testObserversReceiveUserAction()
    {
        $subject = new UserActionSubject();

        // Mock del logger
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains('user-123 performed login'));

        $logger = new ActivityLogger($loggerMock);
        $tracker = new UserMetricsTracker();

        $subject->attach($logger);
        $subject->attach($tracker);

        $action = new UserAction('user-123', 'login', new \DateTimeImmutable());
        $subject->notify($action);

        $this->assertEquals(1, $tracker->getUserActionCount('user-123', 'login'));
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
