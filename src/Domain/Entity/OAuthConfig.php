<?php

namespace App\Domain\Entity;

class OAuthConfig
{
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $redirectUri,
        public array $scopes
    ) {}
}
