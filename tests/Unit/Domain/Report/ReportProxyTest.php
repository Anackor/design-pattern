<?php

namespace App\Tests\Domain\Report\Proxy;

use App\Domain\Report\Proxy\ReportProxy;
use App\Domain\Report\Proxy\LazyReportProxy;
use App\Infrastructure\Security\AccessChecker;
use App\Shared\Security\UserContext;
use PHPUnit\Framework\TestCase;

class ReportProxyTest extends TestCase
{
    public function testGenerateReportWithAccessControl(): void
    {
        $lazyProxyMock = $this->createMock(LazyReportProxy::class);
        $lazyProxyMock->method('generate')
            ->willReturn('Generated Report');

        $userContextMock = $this->createMock(UserContext::class);
        $userContextMock->method('getRoles')
            ->willReturn(['ROLE_USER']);

        $accessChecker = new AccessChecker($userContextMock);

        $reportProxy = new ReportProxy($lazyProxyMock, $accessChecker);

        $generatedReport = $reportProxy->generate();

        $this->assertEquals('Generated Report', $generatedReport);
    }

    public function testGenerateReportWithoutAccess(): void
    {
        $lazyProxyMock = $this->createMock(LazyReportProxy::class);

        $userContextMock = $this->createMock(UserContext::class);
        $userContextMock->method('getRoles')
            ->willReturn([]);

        $accessChecker = new AccessChecker($userContextMock);

        $reportProxy = new ReportProxy($lazyProxyMock, $accessChecker);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Access Denied');

        $reportProxy->generate();
    }
}
