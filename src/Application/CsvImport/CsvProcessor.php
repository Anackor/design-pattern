<?php

namespace App\Application\CsvImport;

/**
 * CSV processor that applies a callback to each row of the file.
 */
class CsvProcessor
{
    public function __construct(
        private readonly iterable $rows
    ) {}

    public function process(callable $handler): void
    {
        foreach ($this->rows as $row) {
            $handler($row);
        }
    }

    public function map(callable $mapper): array
    {
        $mapped = [];

        $this->process(static function (array $row) use (&$mapped, $mapper): void {
            $mapped[] = $mapper($row);
        });

        return $mapped;
    }

    public function filter(callable $predicate): array
    {
        $filtered = [];

        $this->process(static function (array $row) use (&$filtered, $predicate): void {
            if ($predicate($row)) {
                $filtered[] = $row;
            }
        });

        return $filtered;
    }
}
