<?php

namespace App\Domain\Report\Proxy;

use App\Domain\Report\ReportInterface;
use App\Infrastructure\Security\AccessChecker;

class ReportProxy implements ReportInterface
{
    private LazyReportProxy $lazyReportProxy;
    private AccessChecker $accessChecker;

    public function __construct(LazyReportProxy $lazyReportProxy, AccessChecker $accessChecker)
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

    public function getAccessChecker(): AccessChecker
    {
        return $this->accessChecker;
    }
}
