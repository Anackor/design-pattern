<?php

namespace App\Application\Report;

use App\Domain\Report\Proxy\LazyReportProxy;
use App\Domain\Report\Proxy\ReportProxy;
use App\Domain\Report\ReportAccessCheckerInterface;
use App\Domain\Report\ReportInterface;

/**
 * ReportServiceFactory - Factory to create the appropriate proxy for report generation.
 *
 * This factory is responsible for deciding which type of proxy should be created
 * based on the provided parameters. It helps encapsulate the logic of creating
 * either a ReportProxy (with LazyReportProxy and AccessChecker) for report generation.
 */
class ReportServiceFactory
{
    public function __construct(private ReportAccessCheckerInterface $accessChecker) {}

    /**
     * Creates and returns a composed report service with proxies.
     *
     * @return ReportInterface The composed report service.
     */
    public function create(): ReportInterface
    {
        return new ReportProxy(new LazyReportProxy(), $this->accessChecker);
    }
}
