<?php

namespace App\Tests\Application\Factory\Form;

use App\Application\Factory\Form\SlackFormFactory;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\TextFieldInterface;
use PHPUnit\Framework\TestCase;

class SlackFormFactoryTest extends TestCase
{
    private SlackFormFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new SlackFormFactory();
    }

    public function testCreateSlackTextField()
    {
        $textField = $this->factory->createTextField('username');
        $this->assertInstanceOf(TextFieldInterface::class, $textField);
        $this->assertEquals(':pencil: username TextField', $textField->render());
    }

    public function testCreateSlackCheckbox()
    {
        $checkbox = $this->factory->createCheckbox('remember_me');
        $this->assertInstanceOf(CheckboxInterface::class, $checkbox);
        $this->assertEquals(':ballot_box_with_check: remember_me Checkbox', $checkbox->render());
    }

    public function testCreateSlackButton()
    {
        $button = $this->factory->createButton('submit');
        $this->assertInstanceOf(ButtonInterface::class, $button);
        $this->assertEquals(':heavy_plus_sign: submit', $button->render());
    }
}
