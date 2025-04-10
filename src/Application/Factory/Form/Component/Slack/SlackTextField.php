<?php

namespace App\Application\Factory\Form\Component\Slack;

use App\Domain\Form\Component\TextFieldInterface;

class SlackTextField implements TextFieldInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return ":pencil: {$this->label} TextField";
    }
}
