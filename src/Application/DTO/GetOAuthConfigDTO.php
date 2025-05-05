<?php

namespace App\Application\DTO;

use App\Domain\Enum\OAuthProviders;

class GetOAuthConfigDTO
{
    /**
     * The channel must be one of the predefined options.
     * We dynamically extract the allowed values using OAuthProviders::values().
     *
     * @Assert\Choice(
     *     callback={OAuthProviders::class, "values"},
     *     message="Invalid channel '{{ value }}'."
     * )
     */
    private string $provider;

    public function __construct(string $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
