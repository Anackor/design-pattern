<?php

namespace App\Tests\Unit\Application\BuildForm;

use App\Application\BuildForm\BuildFormHandler;
use App\Application\DTO\FormRequestDTO;
use App\Application\Factory\Form\ConsoleFormFactory;
use App\Application\Factory\Form\FormFactoryResolver;
use App\Application\Factory\Form\HtmlFormFactory;
use App\Application\Factory\Form\SlackFormFactory;
use PHPUnit\Framework\TestCase;

class BuildFormHandlerTest extends TestCase
{
    private function createHandler(): BuildFormHandler
    {
        return new BuildFormHandler(new FormFactoryResolver(
            new HtmlFormFactory(),
            new SlackFormFactory(),
            new ConsoleFormFactory()
        ));
    }

    public function testHandleBuildsHtmlFormFromDto(): void
    {
        $handler = $this->createHandler();

        $rendered = $handler->handle(new FormRequestDTO('html', 'Email', 'Terms', 'Submit'));

        $this->assertSame(
            '<label>Email</label><input type="text" />'
            . PHP_EOL
            . '<label>Terms</label><input type="checkbox" />'
            . PHP_EOL
            . '<button>Submit</button>',
            $rendered
        );
    }

    public function testHandleBuildsConsoleFormFromDto(): void
    {
        $handler = $this->createHandler();

        $rendered = $handler->handle(new FormRequestDTO('console', 'Username', 'Remember me', 'Login'));

        $this->assertSame(
            '[Username] [TextField]'
            . PHP_EOL
            . '[Remember me] [ ] Checkbox'
            . PHP_EOL
            . '[Login] [Submit Button]',
            $rendered
        );
    }
}
