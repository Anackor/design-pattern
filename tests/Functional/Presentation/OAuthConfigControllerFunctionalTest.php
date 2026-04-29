<?php

namespace App\Tests\Functional\Presentation;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Domain\Entity\OAuthConfig;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class OAuthConfigControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testGetOAuthConfigReturnsConfigThroughKernel(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new OAuthConfig('client-id', 'secret', 'https://callback', ['email']));

        $this->setTestService(GetOAuthConfigHandler::class, $handler);

        $response = $this->request('GET', '/auth/config', ['provider' => 'google'], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'OAuth configuration loaded',
            'data' => [
                'client_id' => 'client-id',
                'redirect_uri' => 'https://callback',
                'scopes' => ['email'],
            ],
        ], $this->decodeJson($response));
    }

    public function testGetOAuthConfigReturnsJsonBadRequestWhenProviderIsMissing(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(GetOAuthConfigHandler::class, $handler);

        $response = $this->request('GET', '/auth/config', [], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Missing required "provider" parameter.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testGetOAuthConfigReturnsValidationErrorsForUnsupportedProvider(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(GetOAuthConfigHandler::class, $handler);

        $response = $this->request('GET', '/auth/config', ['provider' => 'linkedin'], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $payload = $this->decodeJson($response);
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('provider', $payload['error']['details'][0]['field']);
        $this->assertStringContainsString('Invalid provider', $payload['error']['details'][0]['message']);
        $this->assertStringContainsString('linkedin', $payload['error']['details'][0]['message']);
    }
}
