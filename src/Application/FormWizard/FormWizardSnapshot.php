<?php

namespace App\Application\FormWizard;

/**
 * The Memento in the Memento pattern:
 *
 * This class stores the internal state of the Originator (FormWizard) at
 * a particular point in time. It is immutable and does not allow external
 * modifications to its contents.
 */
class FormWizardSnapshot
{
    public function __construct(
        private readonly int $step,
        private readonly array $data
    ) {}

    public function getStep(): int
    {
        return $this->step;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
