<?php

namespace App\Infrastructure\Export;

use App\Domain\Export\ExportFormatInterface;

class JsonExportFormat implements ExportFormatInterface
{
    public function export(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
