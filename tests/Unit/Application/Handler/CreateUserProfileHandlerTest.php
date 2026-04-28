<?php

namespace App\Tests\Application\Handler;

use App\Application\DTO\UserProfileDTO;
use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Repository\UserProfileRepositoryInterface as UserProfileRepository;
use PHPUnit\Framework\TestCase;

class CreateUserProfileHandlerTest extends TestCase
{
    public function testHandleCreatesUserProfile()
    {
        $userMock = User::register('John Smith', 'john@example.com', UserRole::ADMIN);
        $userRepository = $this->createMock(UserRepository::class);
        $userProfileRepository = $this->createMock(UserProfileRepository::class);
        $this->assertInstanceOf(UserProfileRepository::class, $userProfileRepository);

        // Simulamos que el usuario existe
        $userRepository->method('registeredUserOfId')->willReturn($userMock);

        $handler = new CreateUserProfileHandler($userRepository, $userProfileRepository);
        $dto = new UserProfileDTO(1, '123456789', 'Fake Street 123', '2000-01-01');

        $profile = $handler->handle($dto);

        $this->assertNotNull($profile);
        $this->assertEquals('123456789', $profile->getPhone());
        $this->assertEquals('Fake Street 123', $profile->getAddress());
    }

    public function testHandleReturnsNullIfUserNotFound()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);

        $userRepository->method('registeredUserOfId')->willReturn(null);

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository);
        $dto = new UserProfileDTO(99, '987654321', 'Unknown Street', '1999-05-15');

        $this->assertNull($handler->handle($dto));
    }

    public function testHandleRejectsIncompleteProfileData(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);

        $userRepository->method('registeredUserOfId')->willReturn(User::register('John Smith', 'john@example.com'));

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository);
        $dto = new UserProfileDTO(1, null, 'Fake Street 123', '2000-01-01');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile data is incomplete.');

        $handler->handle($dto);
    }
}
