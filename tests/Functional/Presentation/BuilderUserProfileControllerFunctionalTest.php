<?php

namespace App\Tests\Functional\Presentation;

use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use App\Application\DTO\UserProfileDTO;
use App\Domain\Entity\UserProfile;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class BuilderUserProfileControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testCreateProfileReturnsCreatedResponseThroughKernel(): void
    {
        $profile = $this->createMock(UserProfile::class);
        $profile->method('getId')->willReturn(42);

        $handler = $this->createMock(CreateUserProfileHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (UserProfileDTO $dto): bool {
                return 42 === $dto->userId
                    && '+34600000000' === $dto->phone
                    && 'Main Street 1' === $dto->address
                    && '2000-01-01' === $dto->birthdate;
            }))
            ->willReturn($profile);

        $this->setTestService(CreateUserProfileHandler::class, $handler);

        $response = $this->requestJson('POST', '/users/42/profile', [
            'phone' => '+34600000000',
            'address' => 'Main Street 1',
            'birthdate' => '2000-01-01',
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Profile created',
            'data' => [
                'id' => 42,
            ],
        ], $this->decodeJson($response));
    }

    public function testCreateProfileReturnsValidationErrorsBeforeCallingHandler(): void
    {
        $handler = $this->createMock(CreateUserProfileHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(CreateUserProfileHandler::class, $handler);

        $response = $this->requestJson('POST', '/users/42/profile', [
            'address' => 'Main Street 1',
            'birthdate' => '2000-01-01',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $this->decodeJson($response)['status']);
        $this->assertSame('Validation failed', $this->decodeJson($response)['message']);
        $this->assertSame('validation_failed', $this->decodeJson($response)['error']['type']);
        $this->assertSame('phone', $this->decodeJson($response)['error']['details'][0]['field']);
        $this->assertSame('Phone cannot be empty.', $this->decodeJson($response)['error']['details'][0]['message']);
    }

    public function testCreateProfileReturnsStructuredNotFoundEnvelope(): void
    {
        $handler = $this->createMock(CreateUserProfileHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        $this->setTestService(CreateUserProfileHandler::class, $handler);

        $response = $this->requestJson('POST', '/users/42/profile', [
            'phone' => '+34600000000',
            'address' => 'Main Street 1',
            'birthdate' => '2000-01-01',
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found',
            'error' => ['type' => 'not_found'],
        ], $this->decodeJson($response));
    }
}
