<?php

namespace Tests\Application\CsvImport;

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

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
