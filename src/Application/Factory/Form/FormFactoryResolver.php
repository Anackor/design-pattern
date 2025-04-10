<?php

namespace App\Application\Factory\Form;

use App\Domain\Form\FormFactoryInterface;
use InvalidArgumentException;

class FormFactoryResolver
{
    public function __construct(
        private HtmlFormFactory $htmlFactory,
        private SlackFormFactory $slackFactory,
        private ConsoleFormFactory $consoleFactory
    ) {}

    public function get(string $type): FormFactoryInterface
    {
        return match ($type) {
            'html' => $this->htmlFactory,
            'slack' => $this->slackFactory,
            'console' => $this->consoleFactory,
            default => throw new InvalidArgumentException("Unknown factory type: $type"),
        };
    }
}
