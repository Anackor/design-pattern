<?php
namespace App\Application\Factory\Form;

use App\Application\Factory\Form\Component\Console\ConsoleButton;
use App\Application\Factory\Form\Component\Console\ConsoleCheckbox;
use App\Application\Factory\Form\Component\Console\ConsoleTextField;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\TextFieldInterface;
use App\Domain\Form\FormFactoryInterface;

class ConsoleFormFactory implements FormFactoryInterface
{
    public function createTextField(string $label): TextFieldInterface
    {
        return new ConsoleTextField($label);
    }

    public function createCheckbox(string $label): CheckboxInterface
    {
        return new ConsoleCheckbox($label);
    }

    public function createButton(string $label): ButtonInterface
    {
        return new ConsoleButton($label);
    }
}
