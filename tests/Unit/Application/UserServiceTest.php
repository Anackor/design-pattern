<?php

namespace App\Tests\Unit\Application;

use App\Application\Service\UserService;
use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function testCreateUser()
    {
        // Mock del repositorio
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        // Definir comportamiento del mock
        $userRepository->expects($this->once())
            ->method('addRegisteredUser')
            ->with($this->isInstanceOf(User::class));

        // Crear el servicio con el repositorio mockeado
        $userService = new UserService($userRepository);

        // Llamar al método
        $user = $userService->createUser('john_doe', 'john@example.com');

        // Verificar que el usuario fue creado correctamente
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john_doe', $user->getName());
        $this->assertEquals('john@example.com', $user->getEmail());
    }
}
