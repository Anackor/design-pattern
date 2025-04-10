<?php

namespace App\Application\Factory\Form\Component\Html;

use App\Domain\Form\Component\ButtonInterface;

class HtmlButton implements ButtonInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return "<button>{$this->label}</button>";
    }
}
