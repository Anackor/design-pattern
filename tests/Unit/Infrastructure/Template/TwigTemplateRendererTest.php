<?php

namespace App\Tests\Unit\Infrastructure\Template;

use App\Infrastructure\Template\TwigTemplateRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class TwigTemplateRendererTest extends TestCase
{
    public function testRenderDelegatesToTwigEnvironment(): void
    {
        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('emails/welcome.html.twig', ['name' => 'Alice'])
            ->willReturn('<h1>Hello Alice</h1>');

        $renderer = new TwigTemplateRenderer($twig);

        $this->assertSame(
            '<h1>Hello Alice</h1>',
            $renderer->render('emails/welcome.html.twig', ['name' => 'Alice'])
        );
    }
}
