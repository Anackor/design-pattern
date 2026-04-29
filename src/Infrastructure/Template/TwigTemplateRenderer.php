<?php

namespace App\Infrastructure\Template;

use App\Application\Template\TemplateRendererInterface;
use Twig\Environment;

final class TwigTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(private Environment $twig) {}

    public function render(string $templatePath, array $payload): string
    {
        return $this->twig->render($templatePath, $payload);
    }
}
