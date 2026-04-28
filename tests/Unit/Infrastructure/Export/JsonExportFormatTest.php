<?php

namespace App\Tests\Unit\Infrastructure\Export;

use App\Infrastructure\Export\JsonExportFormat;
use PHPUnit\Framework\TestCase;

class JsonExportFormatTest extends TestCase
{
    public function testExportReturnsPrettyPrintedJson(): void
    {
        $format = new JsonExportFormat();

        $json = $format->export([['id' => 1, 'name' => 'Alice']]);

        $this->assertSame(
            json_encode([['id' => 1, 'name' => 'Alice']], JSON_PRETTY_PRINT),
            $json
        );
    }
}
