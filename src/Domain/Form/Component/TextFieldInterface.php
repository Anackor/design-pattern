<?php

namespace App\Domain\Form\Component;

interface TextFieldInterface
{
    /**
     * Returns the rendered text field as string.
     */
    public function render(): string;
}
