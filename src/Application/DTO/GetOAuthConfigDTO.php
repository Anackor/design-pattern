<?php

namespace App\Application\DTO;

use App\Domain\Enum\OAuthProviders;
use Symfony\Component\Validator\Constraints as Assert;

class GetOAuthConfigDTO
{
    /**
     * The provider must be one of the predefined options.
     * We dynamically extract the allowed values using OAuthProviders::values().
     */
    #[Assert\NotBlank(message: 'Provider cannot be empty.')]
    #[Assert\Choice(
        callback: [OAuthProviders::class, 'values'],
        message: "Invalid provider '{{ value }}'."
    )]
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
