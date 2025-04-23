<?php

namespace App\Application\DTO;

class RenderTemplateDTO
{
    public function __construct(
        public readonly string $templateKey,
        public readonly array $payload
    ) {}
}
