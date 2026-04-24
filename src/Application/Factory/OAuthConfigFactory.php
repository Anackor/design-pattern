<?php

namespace App\Application\Factory;

use App\Domain\Entity\OAuthConfig;
use App\Domain\Enum\OAuthProviders;

/**
 * This class implements the Factory Function pattern to create instances of OAuthConfig based on a given provider string. The factory encapsulates the object creation logic,
 * which is especially useful when the construction involves multiple parameters or varies depending on the input.
 *
 * Factory Function is a simpler alternative to the Factory Method pattern. It typically uses a static function (like this one) instead of requiring inheritance or an abstract class
 *
 * Factory Function:
 * - Simple static function
 * - No inheritance needed
 * - Easy to use when there are a few variants
 *
 * Factory Method:
 * - Requires a class hierarchy
 * - More flexible/extensible
 * - Better suited when new types need to be added frequently
 *
 * In this case, since the configuration per provider is known and limited, Factory Function provides a concise and testable solution for creating configuration objects.
 */
class OAuthConfigFactory
{
    public static function create(string $provider): OAuthConfig
    {
        return match ($provider) {
            OAuthProviders::GOOGLE->value => new OAuthConfig(
                clientId: 'GOOGLE_CLIENT_ID',
                clientSecret: 'GOOGLE_CLIENT_SECRET',
                redirectUri: 'https://example.com/oauth/google/callback',
                scopes: ['email', 'profile']
            ),
            OAuthProviders::GITHUB->value => new OAuthConfig(
                clientId: 'GITHUB_CLIENT_ID',
                clientSecret: 'GITHUB_CLIENT_SECRET',
                redirectUri: 'https://example.com/oauth/github/callback',
                scopes: ['read:user', 'user:email']
            ),
            default => throw new \InvalidArgumentException("Unsupported provider: $provider")
        };
    }
}
