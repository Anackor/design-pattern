<?php

namespace App\Application\Factory\Form\Component\Slack;

use App\Domain\Form\Component\ButtonInterface;

class SlackButton implements ButtonInterface
{
    public function __construct(
        private string $label = 'Default Label'
    ) {
        $this->label = $label;
    }

    public function render(): string
    {
        return ":heavy_plus_sign: {$this->label}";
    }
}
