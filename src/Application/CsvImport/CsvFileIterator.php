<?php

namespace App\Application\CsvImport;

use Iterator;

/**
 * This class demonstrates the Iterator Design Pattern, which provides a standardized
 * way to traverse a collection of items without exposing its internal structure.
 *
 * The Iterator Pattern is especially useful when you need to loop over elements
 * of a complex data structure (e.g., a file, a tree, or a custom collection) in
 * a uniform and encapsulated manner. By implementing PHP's built-in Iterator
 * interface, we allow objects of this class to be used directly in foreach loops,
 * ensuring native compatibility with PHP's iteration mechanisms.
 *
 * PHP provides the Iterator interface with methods such as current(), next(),
 * key(), valid(), and rewind(). Implementing this interface allows our class to
 * integrate seamlessly into the language's control structures and iterators.
 *
 * In this example, the CsvFileIterator handles reading a CSV file line-by-line,
 * providing each row as an associative array. This approach keeps the iteration logic
 * encapsulated and testable, and separates it from the logic of how each CSV row is processed.
 *
 * Benefits:
 * - Encapsulation of iteration logic.
 * - Separation of concerns between iteration and data handling.
 * - Native support for foreach and other iterable patterns in PHP.
 * - Easily mockable and testable in isolation.
 */
class CsvFileIterator implements Iterator
{
    private $file;

    /**
     * @var list<string>
     */
    private array $headers;

    /**
     * @var array<string, string|null>|null
     */
    private ?array $current = null;

    private int $key = -1;

    public function __construct(string $filePath)
    {
        $this->file = fopen($filePath, 'r');
        if (!$this->file) {
            throw new \RuntimeException("Unable to open file: $filePath");
        }

        $this->headers = $this->readHeaders();
        $this->next();
    }

    public function current(): array
    {
        if (!$this->valid()) {
            throw new \LogicException('Cannot read the current CSV row because the iterator is not valid.');
        }

        return $this->current;
    }

    public function key(): int
    {
        return $this->key;
    }

    public function next(): void
    {
        $row = fgetcsv($this->file);
        if ($row === false) {
            $this->current = null;
        } else {
            $this->key++;
            $this->current = $this->combineRow($row);
        }
    }

    public function rewind(): void
    {
        rewind($this->file);
        $this->headers = $this->readHeaders();
        $this->key = -1;
        $this->next();
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    public function __destruct()
    {
        if (is_resource($this->file)) {
            fclose($this->file);
        }
    }

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return list<string>
     */
    private function readHeaders(): array
    {
        $headers = fgetcsv($this->file);

        if ($headers === false) {
            throw new \RuntimeException('CSV file must contain a header row.');
        }

        $normalizedHeaders = array_map(static fn(?string $header): string => trim((string) $header), $headers);

        if (count(array_filter($normalizedHeaders)) === 0) {
            throw new \RuntimeException('CSV file must contain a header row.');
        }

        return $normalizedHeaders;
    }

    /**
     * @param list<string|null> $row
     * @return array<string, string|null>
     */
    private function combineRow(array $row): array
    {
        if (count($row) !== count($this->headers)) {
            throw new \RuntimeException(sprintf(
                'CSV row %d has %d columns, expected %d.',
                $this->key,
                count($row),
                count($this->headers)
            ));
        }

        return array_combine($this->headers, $row);
    }
}
