<?php

namespace App\Tests\Domain\Report\Proxy;

use App\Domain\Report\ReportAccessCheckerInterface;
use App\Domain\Report\Proxy\ReportProxy;
use App\Domain\Report\Proxy\LazyReportProxy;
use PHPUnit\Framework\TestCase;

class ReportProxyTest extends TestCase
{
    public function testGenerateReportWithAccessControl(): void
    {
        $lazyProxyMock = $this->createMock(LazyReportProxy::class);
        $lazyProxyMock->method('generate')
            ->willReturn('Generated Report');

        $accessChecker = $this->createMock(ReportAccessCheckerInterface::class);
        $accessChecker->method('canViewFinancialReports')->willReturn(true);

        $reportProxy = new ReportProxy($lazyProxyMock, $accessChecker);

        $generatedReport = $reportProxy->generate();

        $this->assertEquals('Generated Report', $generatedReport);
    }

    public function testGenerateReportWithoutAccess(): void
    {
        $lazyProxyMock = $this->createMock(LazyReportProxy::class);
        $accessChecker = $this->createMock(ReportAccessCheckerInterface::class);
        $accessChecker->method('canViewFinancialReports')->willReturn(false);

        $reportProxy = new ReportProxy($lazyProxyMock, $accessChecker);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access Denied');

        $reportProxy->generate();
    }
}
