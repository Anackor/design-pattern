<?php

namespace App\Application\CsvImport;

/**
 * Simple service to demonstrate usage of Iterator by counting rows in a CSV.
 */
class CsvRowCounterService
{
    public function countRows(string $filePath): int
    {
        return $this->countRowsWhere($filePath);
    }

    public function countRowsWhere(string $filePath, ?callable $predicate = null): int
    {
        $iterator = new CsvFileIterator($filePath);
        $processor = new CsvProcessor($iterator);

        $count = 0;
        $processor->process(function (array $row) use (&$count, $predicate): void {
            if (null === $predicate || $predicate($row)) {
                $count++;
            }
        });

        return $count;
    }
}
