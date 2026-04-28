<?php

namespace App\Tests\Unit\Application\Report;

use App\Application\DTO\GenerateReportDTO;
use App\Application\Report\GenerateFinancialReportHandler;
use App\Domain\Report\ReportInterface;
use PHPUnit\Framework\TestCase;

class GenerateFinancialReportHandlerTest extends TestCase
{
    public function testHandleGeneratesFinancialReport(): void
    {
        $report = $this->createMock(ReportInterface::class);
        $report->expects($this->once())
            ->method('generate')
            ->willReturn('Generated Report');

        $handler = new GenerateFinancialReportHandler($report);

        $this->assertSame('Generated Report', $handler->handle(new GenerateReportDTO('financial')));
    }

    public function testHandleRejectsUnsupportedReportType(): void
    {
        $handler = new GenerateFinancialReportHandler($this->createMock(ReportInterface::class));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported report type: sales');

        $handler->handle(new GenerateReportDTO('sales'));
    }
}
