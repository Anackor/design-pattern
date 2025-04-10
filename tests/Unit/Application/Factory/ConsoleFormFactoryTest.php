<?php

namespace App\Tests\Application\Factory\Form;

use App\Application\Factory\Form\ConsoleFormFactory;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\TextFieldInterface;
use PHPUnit\Framework\TestCase;

class ConsoleFormFactoryTest extends TestCase
{
    private ConsoleFormFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ConsoleFormFactory();
    }

    public function testCreateConsoleTextField()
    {
        $textField = $this->factory->createTextField('username');
        $this->assertInstanceOf(TextFieldInterface::class, $textField);
        $this->assertEquals('[username] [TextField]', $textField->render());
    }

    public function testCreateConsoleCheckbox()
    {
        $checkbox = $this->factory->createCheckbox('remember_me');
        $this->assertInstanceOf(CheckboxInterface::class, $checkbox);
        $this->assertEquals('[remember_me] [ ] Checkbox', $checkbox->render());
    }

    public function testCreateConsoleButton()
    {
        $button = $this->factory->createButton('submit');
        $this->assertInstanceOf(ButtonInterface::class, $button);
        $this->assertEquals('[submit] [Submit Button]', $button->render());
    }
}
