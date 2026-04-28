<?php

namespace App\Tests\Unit\Application\Handler;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Factory\NotificationFactoryInterface;
use App\Application\Notification\SendNotificationHandler;
use App\Domain\Notification\NotificationInterface;
use App\Tests\Support\InMemoryLogger;
use PHPUnit\Framework\TestCase;

class SendNotificationHandlerTest extends TestCase
{
    public function testSendNotificationCallsCorrectInstanceAndLogsLifecycle(): void
    {
        $dto = new NotificationRequestDTO('Title', 'Hello', 'mail@test.com', 'email');
        $notificationMock = $this->createMock(NotificationInterface::class);
        $notificationMock->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $notificationMock->method('getChannelName')->willReturn('email');

        $factory = $this->createMock(NotificationFactoryInterface::class);
        $factory->method('create')->willReturn($notificationMock);
        $logger = new InMemoryLogger();

        $handler = new SendNotificationHandler($factory, $logger);
        $handler->handle($dto);

        $this->assertCount(2, $logger->records);
        $this->assertSame('notification.send.started', $logger->records[0]['message']);
        $this->assertSame('m***@test.com', $logger->records[0]['context']['receiver_preview']);
        $this->assertSame('notification.send.succeeded', $logger->records[1]['message']);
        $this->assertSame('email', $logger->records[1]['context']['resolved_channel']);
    }

    public function testSendNotificationLogsWarningWhenTransportReturnsFalse(): void
    {
        $dto = new NotificationRequestDTO('Title', 'Hello', 'mail@test.com', 'email');
        $notificationMock = $this->createMock(NotificationInterface::class);
        $notificationMock->method('send')->willReturn(false);
        $notificationMock->method('getChannelName')->willReturn('email');

        $factory = $this->createMock(NotificationFactoryInterface::class);
        $factory->method('create')->willReturn($notificationMock);
        $logger = new InMemoryLogger();

        $handler = new SendNotificationHandler($factory, $logger);
        $handler->handle($dto);

        $this->assertCount(2, $logger->records);
        $this->assertSame('warning', $logger->records[1]['level']);
        $this->assertSame('notification.send.returned_false', $logger->records[1]['message']);
    }

    public function testSendNotificationLogsAndRethrowsFactoryFailures(): void
    {
        $dto = new NotificationRequestDTO('Title', 'Hello', 'mail@test.com', 'email');
        $factory = $this->createMock(NotificationFactoryInterface::class);
        $factory->method('create')->willThrowException(new \RuntimeException('Factory failed'));
        $logger = new InMemoryLogger();

        $handler = new SendNotificationHandler($factory, $logger);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Factory failed');

        try {
            $handler->handle($dto);
        } finally {
            $this->assertCount(2, $logger->records);
            $this->assertSame('notification.send.failed', $logger->records[1]['message']);
            $this->assertInstanceOf(\RuntimeException::class, $logger->records[1]['context']['exception']);
        }
    }

    /**
     * DTO Validation (NotificationRequestDTO) ensures that the data is valid before reaching the Handler or Factory.
     * We do not need to create error paths in these tests, as invalid data will be caught during the DTO validation step.
     */
}
