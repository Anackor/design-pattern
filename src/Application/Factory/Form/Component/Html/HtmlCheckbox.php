<?php

namespace App\Application\Factory\Form\Component\Html;

use App\Domain\Form\Component\CheckboxInterface;

class HtmlCheckbox implements CheckboxInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return '<label>' . $this->label . '</label><input type="checkbox" />';
    }
}
