<?php

namespace App\Tests\Unit\Presentation;

use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use App\Domain\Entity\User;
use App\Domain\Entity\UserProfile;
use App\Presentation\BuilderUserProfileController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BuilderUserProfileControllerTest extends TestCase
{
    public function testCreateProfileReturnsCreatedResponse(): void
    {
        $profile = new UserProfile(
            User::register('Jane Doe', 'jane@example.com'),
            '+34600000000',
            'Main Street 1',
            new \DateTimeImmutable('2000-01-01')
        );

        $handler = $this->createMock(CreateUserProfileHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willReturn($profile);

        $controller = new BuilderUserProfileController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->createProfile(5, $this->jsonRequest([
            'phone' => '+34600000000',
            'address' => 'Main Street 1',
            'birthdate' => '2000-01-01',
        ]));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Profile created',
            'data' => ['id' => null],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCreateProfileReturnsValidationErrors(): void
    {
        $controller = new BuilderUserProfileController(
            $this->createMock(CreateUserProfileHandler::class),
            $this->createValidator($this->violationList('Invalid profile data.')),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );

        $response = $controller->createProfile(5, $this->jsonRequest([]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'payload', 'message' => 'Invalid profile data.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCreateProfileReturnsNotFoundWhenHandlerReturnsNull(): void
    {
        $handler = $this->createMock(CreateUserProfileHandler::class);
        $handler->method('handle')->willReturn(null);

        $controller = new BuilderUserProfileController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->createProfile(5, $this->jsonRequest([
            'phone' => '+34600000000',
            'address' => 'Main Street 1',
            'birthdate' => '2000-01-01',
        ]));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found',
            'error' => ['type' => 'not_found'],
        ], json_decode((string) $response->getContent(), true));
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode([] === $data ? new \stdClass() : $data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'payload', null),
        ]);
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory(new ValidationErrorFormatter());
    }
}
