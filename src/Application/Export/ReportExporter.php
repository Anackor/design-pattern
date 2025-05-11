<?php

namespace App\Application\Export;

/**
 * This class exists separately from other exporters (e.g., UserExporter)
 * to represent a specific export context or data type (in this case, reports).
 * 
 * Although the current export logic is identical, the separation allows:
 * - Independent evolution of each exporter's data transformation.
 * - Custom preprocessing or filtering for different domain models (e.g., reports vs. users).
 * - Clear semantic meaning and separation of concerns in the application layer.
 * 
 * Future implementations may include fetching real report data,
 * applying specific formatting, or enriching the exported content.
 */
class ReportExporter extends AbstractExporter
{
    public function export(array $data): string
    {
        return $this->format->export($data);
    }
}
