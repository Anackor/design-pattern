<?php

namespace App\Domain\Form\Component;

interface CheckboxInterface
{
    /**
     * Returns the rendered checkbox as string.
     */
    public function render(): string;
}
