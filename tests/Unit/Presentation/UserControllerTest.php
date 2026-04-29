<?php

namespace App\Tests\Unit\Presentation;

use App\Application\GetUser\GetUserHandler;
use App\Domain\Entity\User;
use App\Presentation\UserController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    public function testGetUserReturnsSerializedUserData(): void
    {
        $handler = $this->createMock(GetUserHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(User::register('Jane Doe', 'jane@example.com'));

        $controller = new UserController($handler, $this->apiResponseFactory());
        $response = $controller->getUser(7);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'User retrieved',
            'data' => [
                'id' => null,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testGetUserReturnsNotFoundResponse(): void
    {
        $handler = $this->createMock(GetUserHandler::class);
        $handler->method('handle')->willReturn(null);

        $controller = new UserController($handler, $this->apiResponseFactory());
        $response = $controller->getUser(404);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found',
            'error' => ['type' => 'not_found'],
        ], json_decode((string) $response->getContent(), true));
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
