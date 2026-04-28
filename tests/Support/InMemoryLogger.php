<?php

namespace App\Tests\Support;

use Psr\Log\AbstractLogger;
use Stringable;

final class InMemoryLogger extends AbstractLogger
{
    /**
     * @var list<array{level: string, message: string, context: array<mixed, mixed>}>
     */
    public array $records = [];

    /**
     * @param mixed $level
     * @param mixed[] $context
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->records[] = [
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}
