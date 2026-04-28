<?php

namespace App\Tests\Unit\Application\Template;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Singleton\EmailTemplateRegistry;
use App\Application\Template\RenderEmailTemplateHandler;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class RenderEmailTemplateHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        EmailTemplateRegistry::getInstance()->reset();
    }

    protected function tearDown(): void
    {
        EmailTemplateRegistry::getInstance()->reset();
    }

    public function testHandleRendersTemplateResolvedFromRegistry(): void
    {
        EmailTemplateRegistry::getInstance()->register('invoice', 'emails/invoice.html.twig');

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('emails/invoice.html.twig', ['name' => 'Alice'])
            ->willReturn('<h1>Alice</h1>');

        $handler = new RenderEmailTemplateHandler($twig);

        $this->assertSame(
            '<h1>Alice</h1>',
            $handler->handle(new RenderTemplateDTO('invoice', ['name' => 'Alice']))
        );
    }
}
