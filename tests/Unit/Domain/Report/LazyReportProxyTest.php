<?php

namespace App\Tests\Unit\Domain\Report;

use App\Domain\Report\Proxy\LazyReportProxy;
use App\Domain\Report\ReportInterface;
use App\Domain\Report\RealReport;
use PHPUnit\Framework\TestCase;

class LazyReportProxyTest extends TestCase
{
    public function testLazyReportGeneration()
    {
        $lazyReportProxy = new LazyReportProxy();

        $this->assertInstanceOf(LazyReportProxy::class, $lazyReportProxy);

        $reportContent = $lazyReportProxy->generate();

        $this->assertEquals('Generated Report', $reportContent);
    }
}
