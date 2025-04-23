<?php

namespace App\Application\Template;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Singleton\EmailTemplateRegistry;
use Twig\Environment;

class RenderEmailTemplateHandler
{
    public function __construct(private Environment $twig) {}

    public function handle(RenderTemplateDTO $dto): string
    {
        $templatePath = EmailTemplateRegistry::getInstance()->get($dto->templateKey);
        return $this->twig->render($templatePath, $dto->payload);
    }
}
