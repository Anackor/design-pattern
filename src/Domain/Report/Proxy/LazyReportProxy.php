<?php

namespace App\Domain\Report\Proxy;

use App\Domain\Report\ReportInterface;
use App\Domain\Report\FinancialReport;

/**
 * LazyReportProxy - Implements the Proxy design pattern.
 * 
 * This class is a virtual proxy that delays the creation of the real report
 * until the `generate()` method is called. It only creates the real report 
 * instance when it is actually needed, thus improving performance and memory usage
 * when the report generation is not always required.
 * 
 * @see FinancialReport
 */
class LazyReportProxy implements ReportInterface
{
    private ?ReportInterface $realReport = null;

    /**
     * Generate the report.
     *
     * This method will create the real report object if it's not already created,
     * and then delegate the report generation to the real report.
     * 
     * @return string The content of the generated report.
     */
    public function generate(): string
    {
        if ($this->realReport === null) {
            $this->realReport = new FinancialReport(); // Lazy creation of the real report
        }

        return $this->realReport->generate(); // Delegate the generation to the real report
    }
}
