<?php

namespace App\Tests\Unit\Application\Registration;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\DTO\UserDataDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Application\Registration\UserRegistrationFacade;
use App\Application\Service\UserService;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class UserRegistrationFacadeTest extends TestCase
{
    public function testRegisterCreatesUserAndSendsNotification(): void
    {
        $userName = 'Test User';
        $userEmail = 'test@example.com';
        $password = 'password';
        $userData = new UserDataDTO($userName, $userEmail, $password);

        $user = new User();
        $user->setEmail($userEmail);
        $user->setName($userName);

        $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->expects($this->once())
            ->method('createUser')
            ->with($userName, $userEmail)
            ->willReturn($user);

        $sendNotificationHandlerMock = $this->createMock(SendNotificationHandler::class);
        $sendNotificationHandlerMock->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (NotificationRequestDTO $dto) use ($userEmail) {
                return $dto->getReceiver() === $userEmail &&
                    $dto->getChannel() === 'email' &&
                    str_contains($dto->getMessage(), 'Thank you for registering');
            }));

        $facade = new UserRegistrationFacade($userServiceMock, $sendNotificationHandlerMock);
        $result = $facade->register($userData);

        $this->assertSame($user, $result);
    }
}
