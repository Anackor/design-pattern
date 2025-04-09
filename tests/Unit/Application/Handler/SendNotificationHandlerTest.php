<?php

namespace App\Tests\Application\Handler;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Domain\Factory\NotificationFactoryInterface;
use App\Domain\Notification\NotificationInterface;
use PHPUnit\Framework\TestCase;

class SendNotificationHandlerTest extends TestCase
{
    public function testSendNotificationCallsCorrectInstance()
    {
        $dto = new NotificationRequestDTO('email', 'Hello', 'World', 'mail@test.com');
        $notificationMock = $this->createMock(NotificationInterface::class);
        $notificationMock->expects($this->once())->method('send');

        $factory = $this->createMock(NotificationFactoryInterface::class);
        $factory->method('create')->willReturn($notificationMock);

        $handler = new SendNotificationHandler($factory);
        $handler->handle($dto);
    }

    /**
     * DTO Validation (NotificationRequestDTO) ensures that the data is valid before reaching the Handler or Factory.
     * We do not need to create error paths in these tests, as invalid data will be caught during the DTO validation step.
     */
}
