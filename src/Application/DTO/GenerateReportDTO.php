<?php

namespace App\Application\DTO;

class GenerateReportDTO
{
    public string $reportType;

    public function __construct(string $reportType)
    {
        $this->reportType = $reportType;
    }
}
