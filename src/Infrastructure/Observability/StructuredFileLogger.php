<?php

namespace App\Infrastructure\Observability;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * StructuredFileLogger is a small PSR-3 implementation used to teach observability
 * without hiding the mechanism behind external tooling.
 *
 * The logger writes one JSON object per line into a file under `var/log/`.
 * This mirrors a common production practice:
 * - stable event names are used as the log message;
 * - context travels as structured data instead of string concatenation;
 * - complex values such as dates, throwables or domain objects are normalized
 *   before they are written.
 *
 * The class is intentionally simple and verbose because the goal of this project
 * is not only to have logs, but to make the reasoning behind those logs easy to study.
 */
final class StructuredFileLogger extends AbstractLogger
{
    /**
     * @var \Closure(): \DateTimeImmutable
     */
    private \Closure $timestampFactory;

    public function __construct(
        private string $logFilePath,
        private string $channel = 'application',
        ?\Closure $timestampFactory = null
    ) {
        $this->timestampFactory = $timestampFactory ?? static fn(): \DateTimeImmutable => new \DateTimeImmutable();
    }

    /**
     * Writes a PSR-3 record as JSON line.
     *
     * We keep the message as the canonical event name and move everything else
     * into `context` so downstream tooling can filter or aggregate logs reliably.
     *
     * @param mixed $level
     * @param mixed[] $context
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->ensureLogDirectoryExists();

        $record = [
            'timestamp' => ($this->timestampFactory)()->format(\DATE_ATOM),
            'channel' => $this->channel,
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $this->normalizeValue($context),
        ];

        $payload = json_encode($record, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES);
        $written = file_put_contents($this->logFilePath, $payload . PHP_EOL, \FILE_APPEND | \LOCK_EX);

        if (false === $written) {
            throw new \RuntimeException(sprintf('Unable to write log record to "%s".', $this->logFilePath));
        }
    }

    private function ensureLogDirectoryExists(): void
    {
        $directory = dirname($this->logFilePath);
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0o777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create log directory "%s".', $directory));
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function normalizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeValue($item);
            }

            return $normalized;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DATE_ATOM);
        }

        if ($value instanceof \Throwable) {
            return [
                'type' => $value::class,
                'message' => $value->getMessage(),
                'code' => $value->getCode(),
            ];
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if (is_object($value)) {
            return ['type' => $value::class];
        }

        return $value;
    }
}
