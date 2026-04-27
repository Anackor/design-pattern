<?php

namespace App\Domain\Report\Proxy;

use App\Domain\Report\ReportAccessCheckerInterface;
use App\Domain\Report\ReportInterface;

class ReportProxy implements ReportInterface
{
    private LazyReportProxy $lazyReportProxy;
    private ReportAccessCheckerInterface $accessChecker;

    public function __construct(LazyReportProxy $lazyReportProxy, ReportAccessCheckerInterface $accessChecker)
    {
        $this->lazyReportProxy = $lazyReportProxy;
        $this->accessChecker = $accessChecker;
    }

    public function generate(): string
    {
        if (!$this->accessChecker->canViewFinancialReports()) {
            throw new \Exception('Access Denied');
        }

        return $this->lazyReportProxy->generate();
    }

    public function getProxy(): LazyReportProxy
    {
        return $this->lazyReportProxy;
    }

    public function getAccessChecker(): ReportAccessCheckerInterface
    {
        return $this->accessChecker;
    }
}
