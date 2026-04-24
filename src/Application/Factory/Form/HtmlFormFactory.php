<?php

namespace App\Application\Factory\Form;

use App\Application\Factory\Form\Component\Html\HtmlButton;
use App\Application\Factory\Form\Component\Html\HtmlCheckbox;
use App\Application\Factory\Form\Component\Html\HtmlTextField;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\TextFieldInterface;
use App\Domain\Form\FormFactoryInterface;

class HtmlFormFactory implements FormFactoryInterface
{
    public function createTextField(string $label): TextFieldInterface
    {
        return new HtmlTextField($label);
    }

    public function createCheckbox(string $label): CheckboxInterface
    {
        return new HtmlCheckbox($label);
    }

    public function createButton(string $label): ButtonInterface
    {
        return new HtmlButton($label);
    }
}
