<?php

namespace App\Tests\Unit\Application\Report;

use App\Application\Report\ReportServiceFactory;
use App\Domain\Report\Proxy\LazyReportProxy;
use App\Domain\Report\Proxy\ReportProxy;
use App\Domain\Report\ReportAccessCheckerInterface;
use PHPUnit\Framework\TestCase;

class ReportServiceFactoryTest extends TestCase
{
    public function testCreateReturnsConfiguredProxyComposition(): void
    {
        $accessChecker = $this->createMock(ReportAccessCheckerInterface::class);

        $service = (new ReportServiceFactory($accessChecker))->create();

        $this->assertInstanceOf(ReportProxy::class, $service);
        $this->assertInstanceOf(LazyReportProxy::class, $service->getProxy());
        $this->assertSame($accessChecker, $service->getAccessChecker());
    }
}
