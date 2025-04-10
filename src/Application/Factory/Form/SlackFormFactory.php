<?php
namespace App\Application\Factory\Form;

use App\Application\Factory\Form\Component\Slack\SlackButton;
use App\Application\Factory\Form\Component\Slack\SlackCheckbox;
use App\Application\Factory\Form\Component\Slack\SlackTextField;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\TextFieldInterface;
use App\Domain\Form\FormFactoryInterface;

class SlackFormFactory implements FormFactoryInterface
{
    public function createTextField(string $label): TextFieldInterface
    {
        return new SlackTextField($label);
    }

    public function createCheckbox(string $label): CheckboxInterface
    {
        return new SlackCheckbox($label);
    }

    public function createButton(string $label): ButtonInterface
    {
        return new SlackButton($label);
    }
}
