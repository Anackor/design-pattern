<?php

namespace App\Tests\Unit\Application\Auth;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Application\DTO\GetOAuthConfigDTO;
use App\Domain\Enum\OAuthProviders;
use PHPUnit\Framework\TestCase;

class GetOAuthConfigHandlerTest extends TestCase
{
    public function testHandleReturnsConfigForRequestedProvider(): void
    {
        $handler = new GetOAuthConfigHandler();

        $config = $handler->handle(new GetOAuthConfigDTO('google'));

        $this->assertSame('GOOGLE_CLIENT_ID', $config->clientId);
        $this->assertSame('GOOGLE_CLIENT_SECRET', $config->clientSecret);
        $this->assertSame('https://example.com/oauth/google/callback', $config->redirectUri);
        $this->assertSame(['email', 'profile'], $config->scopes);
    }

    public function testDtoReturnsProvidedProvider(): void
    {
        $dto = new GetOAuthConfigDTO('github');

        $this->assertSame('github', $dto->getProvider());
    }

    public function testOAuthProvidersValuesExposeAllowedProviders(): void
    {
        $this->assertSame(['google', 'github'], OAuthProviders::values());
    }
}
