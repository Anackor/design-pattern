<?php

namespace App\Domain\Export;

/**
 * This interface defines the contract for any export format.
 * It allows different implementations (like CSV, JSON, etc.)
 * to be used without changing the exporter classes.
 *
 * It plays the role of "Implementor" in the Bridge pattern,
 * separating the formatting logic from the export logic.
 */

interface ExportFormatInterface
{
    public function export(array $data): string;
}
