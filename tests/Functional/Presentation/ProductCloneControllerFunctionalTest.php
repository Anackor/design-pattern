<?php

namespace App\Tests\Functional\Presentation;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Product\CloneProductHandler;
use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use App\Tests\Functional\Support\JsonHttpKernelTestCase;

class ProductCloneControllerFunctionalTest extends JsonHttpKernelTestCase
{
    public function testCloneReturnsCreatedEnvelopeThroughKernel(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(static function (ProductCloneDTO $dto): bool {
                return 7 === $dto->originalId
                    && 'Cloned product' === $dto->name
                    && 19.99 === $dto->price
                    && 'Description' === $dto->description
                    && null === $dto->categoryId;
            }))
            ->willReturn(new Product('Cloned product', 19.99, 'Description', Category::named('Office')));

        $this->setTestService(CloneProductHandler::class, $handler);

        $response = $this->requestJson('POST', '/products/7/clone', [
            'name' => 'Cloned product',
            'price' => 19.99,
            'description' => 'Description',
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'status' => 'success',
            'message' => 'Product cloned successfully',
            'data' => ['product_id' => null],
        ], $this->decodeJson($response));
    }

    public function testCloneReturnsValidationErrorsForNegativePrice(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(CloneProductHandler::class, $handler);

        $response = $this->requestJson('POST', '/products/7/clone', [
            'price' => -10,
        ]);

        $payload = $this->decodeJson($response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('error', $payload['status']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame('validation_failed', $payload['error']['type']);
        $this->assertSame('price', $payload['error']['details'][0]['field']);
        $this->assertStringContainsString('greater than or equal to 0', $payload['error']['details'][0]['message']);
    }

    public function testCloneReturnsStructuredBadRequestForInvalidJson(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->expects($this->never())->method('handle');

        $this->setTestService(CloneProductHandler::class, $handler);

        $response = $this->requestJson('POST', '/products/7/clone', '{"name":');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Invalid JSON payload.',
            'error' => ['type' => 'bad_request'],
        ], $this->decodeJson($response));
    }

    public function testCloneReturnsStructuredServerErrorWhenHandlerFails(): void
    {
        $handler = $this->createMock(CloneProductHandler::class);
        $handler->expects($this->once())
            ->method('handle')
            ->willThrowException(new \RuntimeException('Clone failed.'));

        $this->setTestService(CloneProductHandler::class, $handler);

        $response = $this->requestJson('POST', '/products/7/clone', [
            'name' => 'Cloned product',
        ]);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'status' => 'error',
            'message' => 'Clone failed.',
            'error' => ['type' => 'internal_server_error'],
        ], $this->decodeJson($response));
    }
}
