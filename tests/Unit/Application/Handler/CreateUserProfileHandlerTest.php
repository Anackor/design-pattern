<?php

namespace App\Tests\Unit\Application\Handler;

use App\Application\DTO\UserProfileDTO;
use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Repository\UserProfileRepositoryInterface as UserProfileRepository;
use App\Tests\Support\InMemoryLogger;
use PHPUnit\Framework\TestCase;

class CreateUserProfileHandlerTest extends TestCase
{
    public function testHandleCreatesUserProfileAndLogsSuccess(): void
    {
        $userMock = User::register('John Smith', 'john@example.com', UserRole::ADMIN);
        $userRepository = $this->createMock(UserRepository::class);
        $userProfileRepository = $this->createMock(UserProfileRepository::class);
        $logger = new InMemoryLogger();
        $this->assertInstanceOf(UserProfileRepository::class, $userProfileRepository);

        // Simulamos que el usuario existe
        $userRepository->method('registeredUserOfId')->willReturn($userMock);

        $handler = new CreateUserProfileHandler($userRepository, $userProfileRepository, $logger);
        $dto = new UserProfileDTO(1, '123456789', 'Fake Street 123', '2000-01-01');

        $profile = $handler->handle($dto);

        $this->assertNotNull($profile);
        $this->assertEquals('123456789', $profile->getPhone());
        $this->assertEquals('Fake Street 123', $profile->getAddress());
        $this->assertCount(2, $logger->records);
        $this->assertSame('user_profile.create.started', $logger->records[0]['message']);
        $this->assertSame('user_profile.create.succeeded', $logger->records[1]['message']);
    }

    public function testHandleReturnsNullIfUserNotFoundAndLogsWarning(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);
        $logger = new InMemoryLogger();

        $userRepository->method('registeredUserOfId')->willReturn(null);

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository, $logger);
        $dto = new UserProfileDTO(99, '987654321', 'Unknown Street', '1999-05-15');

        $this->assertNull($handler->handle($dto));
        $this->assertCount(2, $logger->records);
        $this->assertSame('warning', $logger->records[1]['level']);
        $this->assertSame('user_profile.create.user_not_found', $logger->records[1]['message']);
    }

    public function testHandleRejectsIncompleteProfileDataAndLogsMissingFields(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);
        $logger = new InMemoryLogger();

        $userRepository->method('registeredUserOfId')->willReturn(User::register('John Smith', 'john@example.com'));

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository, $logger);
        $dto = new UserProfileDTO(1, null, 'Fake Street 123', '2000-01-01');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User profile data is incomplete.');

        try {
            $handler->handle($dto);
        } finally {
            $this->assertCount(2, $logger->records);
            $this->assertSame('user_profile.create.invalid_payload', $logger->records[1]['message']);
            $this->assertSame(['phone'], $logger->records[1]['context']['missing_fields']);
        }
    }

    public function testHandleLogsAndRethrowsPersistenceFailures(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $profileRepository = $this->createMock(UserProfileRepository::class);
        $logger = new InMemoryLogger();

        $userRepository->method('registeredUserOfId')->willReturn(User::register('John Smith', 'john@example.com'));
        $profileRepository->method('addProfile')->willThrowException(new \RuntimeException('Database unavailable'));

        $handler = new CreateUserProfileHandler($userRepository, $profileRepository, $logger);
        $dto = new UserProfileDTO(1, '123456789', 'Fake Street 123', '2000-01-01');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database unavailable');

        try {
            $handler->handle($dto);
        } finally {
            $this->assertCount(2, $logger->records);
            $this->assertSame('user_profile.create.failed', $logger->records[1]['message']);
            $this->assertInstanceOf(\RuntimeException::class, $logger->records[1]['context']['exception']);
        }
    }
}
