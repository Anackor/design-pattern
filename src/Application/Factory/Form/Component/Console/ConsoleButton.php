<?php

namespace App\Application\Factory\Form\Component\Console;

use App\Domain\Form\Component\ButtonInterface;

class ConsoleButton implements ButtonInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return "[{$this->label}] [Submit Button]";
    }
}
