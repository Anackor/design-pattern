<?php

namespace App\Application\Export;

/**
 * Exports user data using the selected format.
 */
class UserExporter extends AbstractExporter
{
    public function export(array $data): string
    {
        return $this->format->export($data);
    }
}
