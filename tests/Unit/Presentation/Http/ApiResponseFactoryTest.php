<?php

namespace App\Tests\Unit\Presentation\Http;

use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ApiResponseFactoryTest extends TestCase
{
    public function testSuccessWrapsPayloadInSharedEnvelope(): void
    {
        $factory = new ApiResponseFactory(new ValidationErrorFormatter());

        $response = $factory->success('User retrieved', ['id' => 7]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'User retrieved',
            'data' => ['id' => 7],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testValidationErrorBuildsMachineReadableDetails(): void
    {
        $factory = new ApiResponseFactory(new ValidationErrorFormatter());

        $response = $factory->validationError(new ConstraintViolationList([
            new ConstraintViolation('Invalid provider.', null, [], null, 'provider', null),
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'provider', 'message' => 'Invalid provider.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testHttpErrorMapsStatusCodeToStableType(): void
    {
        $factory = new ApiResponseFactory(new ValidationErrorFormatter());

        $response = $factory->httpError(404, 'User not found');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'User not found',
            'error' => ['type' => 'not_found'],
        ], json_decode((string) $response->getContent(), true));
    }
}
