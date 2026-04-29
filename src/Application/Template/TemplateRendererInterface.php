<?php

namespace App\Application\Template;

interface TemplateRendererInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function render(string $templatePath, array $payload): string;
}
