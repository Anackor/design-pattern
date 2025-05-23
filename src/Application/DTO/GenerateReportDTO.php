<?php

namespace App\Application\Report;

class GenerateReportDTO
{
    public string $reportType;

    public function __construct(string $reportType)
    {
        $this->reportType = $reportType;
    }
}
