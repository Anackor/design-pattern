<?php

namespace App\Tests\Unit\Application\CsvImport;

use App\Application\CsvImport\CsvFileIterator;
use App\Application\CsvImport\CsvProcessor;
use App\Application\CsvImport\CsvRowCounterService;
use PHPUnit\Framework\TestCase;

class CsvRowCounterServiceTest extends TestCase
{
    protected string $filePath;

    protected function setUp(): void
    {
        $this->filePath = __DIR__ . '/fixtures/test.csv';

        if (!file_exists(dirname($this->filePath))) {
            mkdir(dirname($this->filePath), 0o777, true);
        }

        file_put_contents($this->filePath, "name,email\nJohn,john@example.com\nJane,jane@example.com");
    }

    public function testCountsCsvRowsCorrectly(): void
    {
        $filePath = __DIR__ . '/fixtures/test.csv';
        file_put_contents($filePath, "name,email\nAlice,alice@example.com\nBob,bob@example.com");

        $service = new CsvRowCounterService();
        $count = $service->countRows($filePath);

        $this->assertEquals(2, $count);

        unlink($filePath);
    }

    public function testCountsCsvRowsMatchingPredicate(): void
    {
        $service = new CsvRowCounterService();

        $count = $service->countRowsWhere(
            $this->filePath,
            static fn(array $row): bool => str_ends_with($row['email'], '@example.com')
        );

        $this->assertSame(2, $count);
    }

    public function testIteratorExposesHeadersAndRowsForForeach(): void
    {
        $iterator = new CsvFileIterator($this->filePath);

        $this->assertSame(['name', 'email'], $iterator->headers());

        $rows = iterator_to_array($iterator);

        $this->assertSame('John', $rows[0]['name']);
        $this->assertSame('jane@example.com', $rows[1]['email']);
    }

    public function testProcessorCanMapRows(): void
    {
        $processor = new CsvProcessor(new CsvFileIterator($this->filePath));

        $emails = $processor->map(static fn(array $row): string => $row['email']);

        $this->assertSame(['john@example.com', 'jane@example.com'], $emails);
    }

    public function testIteratorRejectsRowsWithUnexpectedColumnCount(): void
    {
        $filePath = __DIR__ . '/fixtures/broken.csv';
        file_put_contents($filePath, "name,email\nAlice");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CSV row 0 has 1 columns, expected 2.');

        iterator_to_array(new CsvFileIterator($filePath));
    }

    protected function tearDown(): void
    {
        foreach (glob(__DIR__ . '/fixtures/*.csv') ?: [] as $fixturePath) {
            unlink($fixturePath);
        }
    }
}
