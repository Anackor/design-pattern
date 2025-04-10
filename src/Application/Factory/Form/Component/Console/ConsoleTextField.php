<?php

namespace App\Application\Factory\Form\Component\Console;

use App\Domain\Form\Component\TextFieldInterface;

class ConsoleTextField implements TextFieldInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return "[{$this->label}] [TextField]";
    }
}
