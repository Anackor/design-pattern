<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Domain\Entity\OAuthConfig;
use App\Presentation\OAuthConfigController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\ValidationErrorFormatter;
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

        $controller = new OAuthConfigController($handler, $this->createValidator(), $this->apiResponseFactory());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'google']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'OAuth configuration loaded',
            'data' => [
                'client_id' => 'client-id',
                'redirect_uri' => 'https://callback',
                'scopes' => ['email'],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testGetOAuthConfigThrowsBadRequestWhenProviderIsMissing(): void
    {
        $controller = new OAuthConfigController(
            $this->createMock(GetOAuthConfigHandler::class),
            $this->createValidator(),
            $this->apiResponseFactory()
        );

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required "provider" parameter.');

        $controller->getOAuthConfig(Request::create('/auth/config', 'GET'));
    }

    public function testGetOAuthConfigReturnsValidationErrors(): void
    {
        $controller = new OAuthConfigController(
            $this->createMock(GetOAuthConfigHandler::class),
            $this->createValidator($this->violationList('Invalid provider.')),
            $this->apiResponseFactory()
        );

        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'linkedin']));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'provider', 'message' => 'Invalid provider.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testGetOAuthConfigReturnsBadRequestWhenHandlerRejectsProvider(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->method('handle')->willThrowException(new \InvalidArgumentException('Unsupported provider: linkedin'));

        $controller = new OAuthConfigController($handler, $this->createValidator(), $this->apiResponseFactory());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'linkedin']));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Unsupported provider: linkedin',
            'error' => ['type' => 'bad_request'],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testGetOAuthConfigReturnsUnexpectedErrorResponse(): void
    {
        $handler = $this->createMock(GetOAuthConfigHandler::class);
        $handler->method('handle')->willThrowException(new \RuntimeException('Boom'));

        $controller = new OAuthConfigController($handler, $this->createValidator(), $this->apiResponseFactory());
        $response = $controller->getOAuthConfig(Request::create('/auth/config', 'GET', ['provider' => 'google']));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Unexpected error.',
            'error' => ['type' => 'internal_server_error'],
        ], json_decode((string) $response->getContent(), true));
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

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
