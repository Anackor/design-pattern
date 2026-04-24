<?php

namespace App\Application\Report;

use App\Domain\Report\FinancialReport;
use App\Domain\Report\Proxy\LazyReportProxy;
use App\Domain\Report\Proxy\ReportProxy;
use App\Domain\Report\ReportInterface;
use App\Infrastructure\Security\AccessChecker;
use App\Shared\Security\UserContext;

/**
 * ReportServiceFactory - Factory to create the appropriate proxy for report generation.
 *
 * This factory is responsible for deciding which type of proxy should be created
 * based on the provided parameters. It helps encapsulate the logic of creating
 * either a ReportProxy (with LazyReportProxy and AccessChecker) for report generation.
 */
class ReportServiceFactory
{
    private UserContext $userContext;

    public function __construct(UserContext $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Creates and returns a composed report service with proxies.
     *
     * @return ReportInterface The composed report service.
     */
    public function create(): ReportInterface
    {
        $virtualProxy = new LazyReportProxy();

        $accessChecker = new AccessChecker($this->userContext);

        return new ReportProxy($virtualProxy, $accessChecker);
    }
}
