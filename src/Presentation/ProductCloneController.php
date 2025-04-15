<?php

namespace App\Presentation;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Product\CloneProductHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCloneController
{
    public function __construct(
        private CloneProductHandler $handler,
        private ValidatorInterface $validator
    ) {}

    #[Route('/products/{id}/clone', methods: ['POST'])]
    public function clone(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ProductCloneDTO(
            $id,
            $data['name'] ?? null,
            $data['price'] ?? null,
            $data['description'] ?? null,
            $data['categoryId'] ?? null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        try {
            $clonedProduct = $this->handler->handle($dto);
            return new JsonResponse([
                'message' => 'Product cloned successfully',
                'product_id' => $clonedProduct->getId(),
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
