<?php

namespace App\Tests\Unit\Presentation;

use App\Application\Product\CloneProductHandler;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Presentation\ProductCloneController;
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

        $controller = new ProductCloneController($handler, $this->createValidator());
        $response = $controller->clone(7, $this->jsonRequest([
            'name' => 'Cloned product',
            'price' => 19.99,
            'description' => 'Description',
            'categoryId' => 2,
        ]));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'message' => 'Product cloned successfully',
            'product_id' => null,
        ], json_decode((string) $response->getContent(), true));
    }

    public function testCloneReturnsValidationErrors(): void
    {
        $controller = new ProductCloneController(
            $this->createMock(CloneProductHandler::class),
            $this->createValidator($this->violationList('Invalid clone payload.'))
        );

        $response = $controller->clone(7, $this->jsonRequest([
            'price' => -10,
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Invalid clone payload.', (string) $response->getContent());
    }

    public function testCloneReturnsServerErrorWhenHandlerFails(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->method('handle')->willThrowException(new \RuntimeException('Clone failed.'));

        $controller = new ProductCloneController($handler, $this->createValidator());
        $response = $controller->clone(7, $this->jsonRequest([
            'name' => 'Cloned product',
        ]));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['error' => 'Clone failed.'], json_decode((string) $response->getContent(), true));
    }

    private function createValidator(?ConstraintViolationList $violations = null): ValidatorInterface
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($violations ?? new ConstraintViolationList());

        return $validator;
    }

    private function jsonRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));
    }

    private function violationList(string $message): ConstraintViolationList
    {
        return new ConstraintViolationList([
            new ConstraintViolation($message, null, [], null, 'payload', null),
        ]);
    }
}
