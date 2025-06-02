<?php

namespace App\Application\CsvImport;

/**
 * CSV processor that applies a callback to each row of the file.
 */
class CsvProcessor
{
    public function __construct(
        private readonly CsvFileIterator $iterator
    ) {}

    public function process(callable $handler): void
    {
        foreach ($this->iterator as $row) {
            $handler($row);
        }
    }
}
