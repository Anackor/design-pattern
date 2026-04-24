<?php

namespace App\Application\Export;

use App\Domain\Export\ExportFormatInterface;

/**
 * This abstract class defines the base exporter logic.
 * It accepts an ExportFormatInterface in the constructor
 * and delegates the export formatting to it.
 *
 * This is the "Abstraction" in the Bridge pattern. It allows
 * exporters (like ReportExporter, UserExporter...) to work
 * independently from the formatting logic.
 */
abstract class AbstractExporter
{
    protected ExportFormatInterface $format;

    public function __construct(ExportFormatInterface $format)
    {
        $this->format = $format;
    }

    /**
     * Each concrete exporter must provide the data to be exported.
     */
    abstract public function export(array $data): string;
}
