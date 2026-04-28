<?php

namespace App\Tests\Unit\Application\GetUser;

use App\Application\GetUser\GetUserHandler;
use App\Application\GetUser\GetUserQuery;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class GetUserHandlerTest extends TestCase
{
    public function testHandleReturnsRegisteredUserFromRepository(): void
    {
        $user = User::register('Jane Doe', 'jane@example.com');
        $query = new GetUserQuery(42);

        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('registeredUserOfId')
            ->with(42)
            ->willReturn($user);

        $handler = new GetUserHandler($repository);

        $this->assertSame($user, $handler->handle($query));
        $this->assertSame(42, $query->userId);
    }

    public function testHandleReturnsNullWhenUserIsNotFound(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->method('registeredUserOfId')->with(404)->willReturn(null);

        $handler = new GetUserHandler($repository);

        $this->assertNull($handler->handle(new GetUserQuery(404)));
    }
}
