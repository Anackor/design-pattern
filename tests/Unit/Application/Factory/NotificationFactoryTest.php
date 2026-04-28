<?php

namespace App\Tests\Unit\Application\Factory;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Factory\NotificationFactory;
use App\Domain\Enum\NotificationChannel;
use App\Domain\Notification\EmailNotification;
use App\Domain\Notification\SlackNotification;
use App\Domain\Notification\SmsNotification;
use App\Domain\Notification\WebhookNotification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NotificationFactoryTest extends TestCase
{
    public static function notificationProvider(): array
    {
        return [
            'email' => ['email', EmailNotification::class, 'email'],
            'sms' => ['sms', SmsNotification::class, 'sms'],
            'webhook' => ['webhook', WebhookNotification::class, 'webhook'],
            'slack' => ['slack', SlackNotification::class, 'slack'],
        ];
    }

    #[DataProvider('notificationProvider')]
    public function testCreateReturnsExpectedNotification(
        string $channel,
        string $expectedClass,
        string $expectedChannelName
    ): void {
        $factory = new NotificationFactory();
        $dto = new NotificationRequestDTO('Title', 'Message body', 'receiver@example.com', $channel);

        $notification = $factory->create($dto);

        $this->assertInstanceOf($expectedClass, $notification);
        $this->assertSame($expectedChannelName, $notification->getChannelName());
        $this->assertTrue($notification->send('Title', 'receiver@example.com', 'Message body'));
    }

    public function testCreateRejectsUnsupportedChannel(): void
    {
        $this->expectException(\ValueError::class);

        (new NotificationFactory())->create(
            new NotificationRequestDTO('Title', 'Message body', 'receiver@example.com', 'pagerduty')
        );
    }

    public function testNotificationChannelValuesExposeSupportedChannels(): void
    {
        $this->assertSame(['email', 'sms', 'webhook', 'slack'], NotificationChannel::values());
    }
}
