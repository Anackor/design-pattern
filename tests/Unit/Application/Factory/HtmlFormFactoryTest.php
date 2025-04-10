<?php

namespace App\Tests\Application\Factory\Form;

use App\Application\Factory\Form\HtmlFormFactory;
use App\Domain\Form\Component\CheckboxInterface;
use App\Domain\Form\Component\ButtonInterface;
use App\Domain\Form\Component\TextFieldInterface;
use PHPUnit\Framework\TestCase;

class HtmlFormFactoryTest extends TestCase
{
    private HtmlFormFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new HtmlFormFactory();
    }

    public function testCreateHtmlTextField()
    {
        $textField = $this->factory->createTextField('username');
        $this->assertInstanceOf(TextFieldInterface::class, $textField);
        $this->assertEquals('<label>username</label><input type="text" />', $textField->render());
    }

    public function testCreateHtmlCheckbox()
    {
        $checkbox = $this->factory->createCheckbox('remember_me');
        $this->assertInstanceOf(CheckboxInterface::class, $checkbox);
        $this->assertEquals('<label>remember_me</label><input type="checkbox" />', $checkbox->render());
    }

    public function testCreateHtmlButton()
    {
        $button = $this->factory->createButton('submit');
        $this->assertInstanceOf(ButtonInterface::class, $button);
        $this->assertEquals('<button>submit</button>', $button->render());
    }
}
