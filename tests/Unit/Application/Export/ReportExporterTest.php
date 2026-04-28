<?php

namespace App\Tests\Unit\Application\Export;

use App\Application\Export\ReportExporter;
use App\Domain\Export\ExportFormatInterface;
use PHPUnit\Framework\TestCase;

class ReportExporterTest extends TestCase
{
    public function testExportUsesGivenFormat(): void
    {
        $mockFormat = $this->createMock(ExportFormatInterface::class);
        $mockFormat->expects($this->once())
            ->method('export')
            ->with([['report' => 'financial']])
            ->willReturn('mocked report export');

        $exporter = new ReportExporter($mockFormat);

        $this->assertSame('mocked report export', $exporter->export([['report' => 'financial']]));
    }
}
