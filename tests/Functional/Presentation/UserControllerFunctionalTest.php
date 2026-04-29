<?php

namespace App\Tests\Functional\Presentation;

use App\Application\GetUser\GetUserHandler;
use App\Domain\Entity\User;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class UserControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testGetUserReturnsSerializedUserThroughKernel(): void
    {
        $handler = $this->createMock(GetUserHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(User::register('Jane Doe', 'jane@example.com'));

        $this->setTestService(GetUserHandler::class, $handler);

        $response = $this->request('GET', '/users/7', [], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'User retrieved',
            'data' => [
                'id' => null,
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ], $this->decodeJson($response));
    }

    public function testGetUserReturnsStructuredNotFoundEnvelopeThroughKernel(): void
    {
        $handler = $this->createMock(GetUserHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        $this->setTestService(GetUserHandler::class, $handler);

        $response = $this->request('GET', '/users/404', [], '', [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found',
            'error' => ['type' => 'not_found'],
        ], $this->decodeJson($response));
    }
}
