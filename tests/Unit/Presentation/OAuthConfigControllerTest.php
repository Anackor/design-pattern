<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Domain\Entity\OAuthConfig;
use App\Presentation\OAuthConfigController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OAuthConfigControllerTest extends TestCase
{
    public function testGetOAuthConfigReturnsConfigData(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new OAuthConfig('client-id', 'secret', 'https://callback', ['email']));

        $controller = new OAuthConfigController($handler, $this->createValidator());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'google']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'client_id' => 'client-id',
            'redirect_uri' => 'https://callback',
            'scopes' => ['email'],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testGetOAuthConfigThrowsBadRequestWhenProviderIsMissing(): void
    {
        $controller = new OAuthConfigController($this->createMock(GetOAuthConfigHandler::class), $this->createValidator());

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required "provider" parameter.');

        $controller->getOAuthConfig(Request::create('/auth/config', 'GET'));
    }

    public function testGetOAuthConfigReturnsValidationErrors(): void
    {
        $controller = new OAuthConfigController(
            $this->createMock(GetOAuthConfigHandler::class),
            $this->createValidator($this->violationList('Invalid provider.'))
        );

        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'linkedin']));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Validation failed', (string) $response->getContent());
    }

    public function testGetOAuthConfigReturnsBadRequestWhenHandlerRejectsProvider(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('Unsupported provider: linkedin'));

        $controller = new OAuthConfigController($handler, $this->createValidator());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'linkedin']));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(['error' => 'Unsupported provider: linkedin'], json_decode((string) $response->getContent(), true));
    }

    public function testGetOAuthConfigReturnsUnexpectedErrorResponse(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->method('handle')->willThrowException(new \RuntimeException('Boom'));

        $controller = new OAuthConfigController($handler, $this->createValidator());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'google']));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['error' => 'Unexpected error.'], json_decode((string) $response->getContent(), true));
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'provider', null),
        ]);
    }
}
