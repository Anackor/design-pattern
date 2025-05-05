<?php

namespace App\Tests\Application\Factory;

use App\Application\Factory\OAuthConfigFactory;
use App\Domain\Entity\OAuthConfig;
use PHPUnit\Framework\TestCase;

class OAuthConfigFactoryTest extends TestCase
{
    public function testCreateGoogleConfig(): void
    {
        $config = OAuthConfigFactory::create('google');

        $this->assertInstanceOf(OAuthConfig::class, $config);
        $this->assertSame('GOOGLE_CLIENT_ID', $config->clientId);
        $this->assertSame('GOOGLE_CLIENT_SECRET', $config->clientSecret);
        $this->assertSame('https://example.com/oauth/google/callback', $config->redirectUri);
        $this->assertEquals(['email', 'profile'], $config->scopes);
    }

    public function testCreateGithubConfig(): void
    {
        $config = OAuthConfigFactory::create('github');

        $this->assertInstanceOf(OAuthConfig::class, $config);
        $this->assertSame('GITHUB_CLIENT_ID', $config->clientId);
        $this->assertSame('GITHUB_CLIENT_SECRET', $config->clientSecret);
        $this->assertSame('https://example.com/oauth/github/callback', $config->redirectUri);
        $this->assertEquals(['read:user', 'user:email'], $config->scopes);
    }

    public function testInvalidProviderThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported provider: linkedin');

        OAuthConfigFactory::create('linkedin');
    }
}
