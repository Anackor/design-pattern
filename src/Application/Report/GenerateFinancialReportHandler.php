<?php

namespace App\Application\Report;

use App\Application\DTO\GenerateReportDTO;
use App\Domain\Report\ReportInterface;

/**
 * The Proxy Pattern is a structural design pattern that provides an object representing another object.
 * It allows controlling access to the real object by introducing an intermediary (proxy) which can
 * perform additional tasks like access control, lazy loading, or performance optimizations.
 *
 * In this pattern, the proxy acts as a surrogate for the real object, allowing the system to defer
 * expensive operations (like object creation or data retrieval) until they are actually needed. It can
 * also manage permissions, ensuring that only authorized users or components can interact with the real object.
 *
 * This pattern promotes cleaner and more modular code by separating concerns. The proxy can handle
 * additional responsibilities like caching, logging, or access checks, without burdening the real object or
 * the main logic of the system. By using proxies, systems can be made more flexible and scalable,
 * while maintaining efficiency in resource-intensive operations.
 */
class GenerateFinancialReportHandler
{
    private ReportInterface $report;

    public function __construct(ReportInterface $report)
    {
        $this->report = $report;
    }

    public function handle(GenerateReportDTO $dto): string
    {
        if ($dto->reportType !== 'financial') {
            throw new \InvalidArgumentException('Unsupported report type: ' . $dto->reportType);
        }

        return $this->report->generate();
    }
}
