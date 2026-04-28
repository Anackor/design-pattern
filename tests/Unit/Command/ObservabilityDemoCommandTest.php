<?php

namespace App\Tests\Unit\Command;

use App\Application\Factory\NotificationFactory;
use App\Application\Notification\SendNotificationHandler;
use App\Application\UserActivity\Observer\ActivityLogger;
use App\Application\UserActivity\UserActionDispatcher;
use App\Application\UserActivity\UserActionSubject;
use App\Command\ObservabilityDemoCommand;
use App\Infrastructure\Observability\StructuredFileLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ObservabilityDemoCommandTest extends TestCase
{
    private string $logFilePath;

    protected function tearDown(): void
    {
        if (isset($this->logFilePath) && is_file($this->logFilePath)) {
            unlink($this->logFilePath);
        }

        if (isset($this->logFilePath)) {
            $directory = dirname($this->logFilePath);
            @rmdir($directory);
            @rmdir(dirname($directory));
        }
    }

    public function testCommandGeneratesAndDisplaysStructuredRecords(): void
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'observability-demo-' . uniqid('', true);
        $this->logFilePath = $directory . DIRECTORY_SEPARATOR . 'observability.log';

        $logger = new StructuredFileLogger(
            $this->logFilePath,
            'application',
            static fn(): \DateTimeImmutable => new \DateTimeImmutable('2026-04-28T12:00:00+00:00')
        );

        $subject = new UserActionSubject();
        $subject->attach(new ActivityLogger($logger));

        $command = new ObservabilityDemoCommand(
            new UserActionDispatcher($subject),
            new SendNotificationHandler(new NotificationFactory(), $logger),
            $this->logFilePath
        );

        $tester = new CommandTester($command);
        $exitCode = $tester->execute(['--reset-log' => true]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Generating structured observability demo...', $tester->getDisplay());
        $this->assertStringContainsString('user.activity.recorded', $tester->getDisplay());
        $this->assertStringContainsString('notification.send.started', $tester->getDisplay());
        $this->assertStringContainsString('notification.send.succeeded', $tester->getDisplay());

        $fileContents = (string) file_get_contents($this->logFilePath);
        $this->assertStringContainsString('user.activity.recorded', $fileContents);
        $this->assertStringContainsString('notification.send.succeeded', $fileContents);
    }
}
