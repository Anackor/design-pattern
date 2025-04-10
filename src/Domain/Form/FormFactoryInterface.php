<?php

namespace App\Domain\Form;

use App\Domain\Form\Component\TextFieldInterface;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\ButtonInterface;

interface FormFactoryInterface
{
    public function createTextField(string $label): TextFieldInterface;
    public function createCheckbox(string $label): CheckboxInterface;
    public function createButton(string $label): ButtonInterface;
}
