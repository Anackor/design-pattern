<?php

namespace App\Domain\Form\Component;

interface ButtonInterface
{
    /**
     * Returns the rendered button as string.
     */
    public function render(): string;
}
