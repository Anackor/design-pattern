<?php

namespace App\Domain\Report;

interface ReportInterface
{
    /**
     * Generates the report content.
     *
     * @return string
     */
    public function generate(): string;
}
