<?php

namespace App\Tests\Unit\Infrastructure\Export;

use App\Infrastructure\Export\CsvExportFormat;
use PHPUnit\Framework\TestCase;

class CsvExportFormatTest extends TestCase
{
    public function testExportReturnsCorrectCsv(): void
    {
        $format = new CsvExportFormat();
        $data = [['id' => 1, 'name' => 'Alice'], ['id' => 2, 'name' => 'Bob']];

        $csv = $format->export($data);

        $expected = "id,name\n1,Alice\n2,Bob\n";
        $this->assertEquals($expected, $csv);
    }
}
