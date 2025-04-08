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
    public function testSendNotificationCallsCorrectInstance()
    {
        $dto = new NotificationRequestDTO('email', 'Hello', 'World', 'mail@test.com');
        $notificationMock = $this->createMock(NotificationInterface::class);
        $notificationMock->expects($this->once())->method('send');

        $factory = $this->createMock(NotificationFactory::class);
        $factory->method('create')->willReturn($notificationMock);

        $handler = new SendNotificationHandler($factory);
        $handler->handle($dto);
    }

    public function testHandleReturnsNullIfUserNotFound()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);

        $userRepository->method('findById')->willReturn(null);

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository);
        $dto = new UserProfileDTO(99, '987654321', 'Unknown Street', '1999-05-15');

        $this->assertNull($handler->handle($dto));
    }
}
