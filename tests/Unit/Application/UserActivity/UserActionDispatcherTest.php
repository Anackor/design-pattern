<?php

namespace App\Tests\Unit\Application\UserActivity;

use App\Application\UserActivity\UserAction;
use App\Application\UserActivity\UserActionDispatcher;
use App\Application\UserActivity\UserActionSubjectInterface;
use PHPUnit\Framework\TestCase;

class UserActionDispatcherTest extends TestCase
{
    public function testRecordActionCreatesAndDispatchesUserAction(): void
    {
        $subject = $this->createMock(UserActionSubjectInterface::class);
        $subject->expects($this->once())
            ->method('notify')
            ->with($this->callback(static function (UserAction $action): bool {
                return $action->userId === 'user-123'
                    && $action->actionType === 'login'
                    && $action->timestamp->getTimestamp() > 0;
            }));

        $dispatcher = new UserActionDispatcher($subject);
        $dispatcher->recordAction('user-123', 'login');
    }
}
