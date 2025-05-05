<?php

namespace App\Application\Auth;

use App\Application\DTO\GetOAuthConfigDTO;
use App\Application\Factory\OAuthConfigFactory;
use App\Domain\Entity\OAuthConfig;

class GetOAuthConfigHandler
{
    public function handle(GetOAuthConfigDTO $dto): OAuthConfig
    {
        return OAuthConfigFactory::create($dto);
    }
}
