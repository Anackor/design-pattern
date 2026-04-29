<?php

namespace App\Presentation;

use App\Application\DTO\FormRequestDTO;
use App\Application\BuildForm\BuildFormHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormController
{
    public function __construct(
        private BuildFormHandler $handler,
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/form', methods: ['POST'])]
    public function build(Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);
        $dto = new FormRequestDTO(
            $data['type'] ?? '',
            $data['textFieldLabel'] ?? '',
            $data['checkboxLabel'] ?? '',
            $data['buttonLabel'] ?? ''
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $formRendered = $this->handler->handle($dto);
            return $this->apiResponseFactory->success('Form rendered', ['form' => $formRendered]);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
