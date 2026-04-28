<?php

namespace App\Tests\Unit\Application\Export;

use App\Application\Export\UserExporter;
use App\Domain\Export\ExportFormatInterface;
use PHPUnit\Framework\TestCase;

class UserExporterTest extends TestCase
{
    public function testExportUsesGivenFormat(): void
    {
        $mockFormat = $this->createMock(ExportFormatInterface::class);
        $mockFormat->expects($this->once())
            ->method('export')
            ->with([['name' => 'Test']])
            ->willReturn('mocked content');

        $exporter = new UserExporter($mockFormat);
        $result = $exporter->export([['name' => 'Test']]);

        $this->assertEquals('mocked content', $result);
    }
}
