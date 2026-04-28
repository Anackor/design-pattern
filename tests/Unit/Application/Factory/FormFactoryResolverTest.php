<?php

namespace App\Tests\Unit\Application\Factory;

use App\Application\Factory\Form\FormFactoryResolver;
use App\Application\Factory\Form\HtmlFormFactory;
use App\Application\Factory\Form\SlackFormFactory;
use App\Application\Factory\Form\ConsoleFormFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FormFactoryResolverTest extends TestCase
{
    private FormFactoryResolver $resolver;
    private HtmlFormFactory $htmlFactory;
    private SlackFormFactory $slackFactory;
    private ConsoleFormFactory $consoleFactory;

    protected function setUp(): void
    {
        $this->htmlFactory = $this->createMock(HtmlFormFactory::class);
        $this->slackFactory = $this->createMock(SlackFormFactory::class);
        $this->consoleFactory = $this->createMock(ConsoleFormFactory::class);

        $this->resolver = new FormFactoryResolver(
            $this->htmlFactory,
            $this->slackFactory,
            $this->consoleFactory
        );
    }

    public function testGetHtmlFactory()
    {
        $factory = $this->resolver->get('html');
        $this->assertInstanceOf(HtmlFormFactory::class, $factory);
    }

    public function testGetSlackFactory()
    {
        $factory = $this->resolver->get('slack');
        $this->assertInstanceOf(SlackFormFactory::class, $factory);
    }

    public function testGetConsoleFactory()
    {
        $factory = $this->resolver->get('console');
        $this->assertInstanceOf(ConsoleFormFactory::class, $factory);
    }

    public function testGetUnknownFactoryThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown factory type: unknown');

        $this->resolver->get('unknown');
    }
}
