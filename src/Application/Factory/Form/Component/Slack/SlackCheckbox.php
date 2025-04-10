<?php

namespace App\Application\Factory\Form\Component\Slack;

use App\Domain\Form\Component\CheckboxInterface;

class SlackCheckbox implements CheckboxInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return ":ballot_box_with_check: {$this->label} Checkbox";
    }
}
