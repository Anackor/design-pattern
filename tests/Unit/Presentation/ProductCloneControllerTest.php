<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Product\CloneProductHandler;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Presentation\ProductCloneController;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use App\Presentation\Http\ValidationErrorFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCloneControllerTest extends TestCase
{
    public function testCloneReturnsCreatedResponse(): void
    {
        $product = new Product('Cloned product', 19.99, 'Description', Category::named('Office'));

        $handler = $this->createMock(CloneProductHandler::class);
        $handler->expects($this->once())->method('handle')->willReturn($product);

        $controller = new ProductCloneController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->clone(7, $this->jsonRequest([
            'name' => 'Cloned product',
            'price' => 19.99,
            'description' => 'Description',
            'categoryId' => 2,
        ]));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Product cloned successfully',
            'data' => [
                'product_id' => null,
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCloneReturnsValidationErrors(): void
    {
        $controller = new ProductCloneController(
            $this->createMock(CloneProductHandler::class),
            $this->createValidator($this->violationList('Invalid clone payload.')),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );

        $response = $controller->clone(7, $this->jsonRequest([
            'price' => -10,
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Validation failed',
            'error' => [
                'type' => 'validation_failed',
                'details' => [
                    ['field' => 'payload', 'message' => 'Invalid clone payload.'],
                ],
            ],
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCloneReturnsServerErrorWhenHandlerFails(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->method('handle')->willThrowException(new \RuntimeException('Clone failed.'));

        $controller = new ProductCloneController(
            $handler,
            $this->createValidator(),
            new JsonRequestDecoder(),
            $this->apiResponseFactory()
        );
        $response = $controller->clone(7, $this->jsonRequest([
            'name' => 'Cloned product',
        ]));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Clone failed.',
            'error' => ['type' => 'internal_server_error'],
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
