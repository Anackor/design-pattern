<?php

namespace App\Domain\Report;

interface ReportAccessCheckerInterface
{
    public function canViewFinancialReports(): bool;
}
