<?php

namespace App\Application\Template;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Singleton\EmailTemplateRegistry;

class RenderEmailTemplateHandler
{
    public function __construct(private TemplateRendererInterface $templateRenderer) {}

    public function handle(RenderTemplateDTO $dto): string
    {
        $templatePath = EmailTemplateRegistry::getInstance()->get($dto->templateKey);

        return $this->templateRenderer->render($templatePath, $dto->payload);
    }
}
