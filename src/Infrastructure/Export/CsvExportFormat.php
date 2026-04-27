<?php

namespace App\Infrastructure\Export;

use App\Domain\Export\ExportFormatInterface;

/**
 * This class implements the ExportFormatInterface and provides
 * the logic for formatting data as CSV.
 *
 * It is the "ConcreteImplementor" in the Bridge pattern, and
 * can easily be replaced or extended with other formats like
 * JSON, XML, etc., without modifying the exporters.
 */
class CsvExportFormat implements ExportFormatInterface
{
    public function export(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        fputcsv($output, array_keys($data[0]));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
