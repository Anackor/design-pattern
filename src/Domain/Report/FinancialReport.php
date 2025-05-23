<?php

namespace App\Domain\Report;

class FinancialReport implements ReportInterface
{
    public function generate(): string
    {
        return 'Generated Report';
    }
}
