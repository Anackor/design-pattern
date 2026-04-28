<?php

namespace App\Tests\Integration\Application\Export;

use App\Application\Export\UserExporter;
use App\Infrastructure\Export\CsvExportFormat;
use PHPUnit\Framework\TestCase;

class UserExporterIntegrationTest extends TestCase
{
    public function testCsvExportWithUserExporter(): void
    {
        $format = new CsvExportFormat();
        $exporter = new UserExporter($format);

        $data = [['id' => 1, 'name' => 'Alice']];
        $result = $exporter->export($data);

        $expected = "id,name\n1,Alice\n";
        $this->assertEquals($expected, $result);
    }
}
