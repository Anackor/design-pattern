<?php

namespace App\Tests\Unit\Infrastructure\Observability;

use App\Infrastructure\Observability\StructuredFileLogger;
use PHPUnit\Framework\TestCase;

class StructuredFileLoggerTest extends TestCase
{
    private ?string $logFilePath = null;

    protected function tearDown(): void
    {
        if (null !== $this->logFilePath && is_file($this->logFilePath)) {
            unlink($this->logFilePath);
        }

        if (null === $this->logFilePath) {
            return;
        }

        $directory = dirname($this->logFilePath);
        if (is_dir($directory)) {
            @rmdir($directory);
            @rmdir(dirname($directory));
        }
    }

    public function testLoggerWritesStructuredJsonLine(): void
    {
        $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'structured-logger-' . uniqid('', true);
        $this->logFilePath = $directory . DIRECTORY_SEPARATOR . 'observability.log';
        $fixedNow = new \DateTimeImmutable('2026-04-28T10:15:00+00:00');

        $logger = new StructuredFileLogger(
            $this->logFilePath,
            'application',
            static fn(): \DateTimeImmutable => $fixedNow
        );

        $logger->info('user_profile.create.succeeded', [
            'user_id' => 42,
            'occurred_at' => new \DateTimeImmutable('2026-04-28T10:00:00+00:00'),
            'exception' => new \RuntimeException('Boom'),
            'subject' => new class {},
        ]);

        $payload = file_get_contents($this->logFilePath);
        $record = json_decode(trim((string) $payload), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('2026-04-28T10:15:00+00:00', $record['timestamp']);
        $this->assertSame('application', $record['channel']);
        $this->assertSame('info', $record['level']);
        $this->assertSame('user_profile.create.succeeded', $record['message']);
        $this->assertSame(42, $record['context']['user_id']);
        $this->assertSame('2026-04-28T10:00:00+00:00', $record['context']['occurred_at']);
        $this->assertSame(\RuntimeException::class, $record['context']['exception']['type']);
        $this->assertSame('Boom', $record['context']['exception']['message']);
        $this->assertArrayHasKey('type', $record['context']['subject']);
    }
}
