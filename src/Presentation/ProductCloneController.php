<?php

namespace App\Presentation;

use App\Application\DTO\ProductCloneDTO;
use App\Application\Product\CloneProductHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCloneController
{
    public function __construct(
        private CloneProductHandler $handler,
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/products/{id}/clone', methods: ['POST'])]
    public function clone(int $id, Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        $dto = new ProductCloneDTO(
            $id,
            $data['name'] ?? null,
            $data['price'] ?? null,
            $data['description'] ?? null,
            $data['categoryId'] ?? null,
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $clonedProduct = $this->handler->handle($dto);
            return $this->apiResponseFactory->success(
                'Product cloned successfully',
                ['product_id' => $clonedProduct->getId()],
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
