<?php

namespace App\Presentation;

use App\Application\DTO\FormRequestDTO;
use App\Application\BuildForm\BuildFormHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormController
{
    public function __construct(
        private BuildFormHandler $handler,
        private ValidatorInterface $validator
    ) {}

    #[Route('/form', methods: ['POST'])]
    public function build(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new FormRequestDTO(
            $data['type'] ?? '',
            $data['textFieldLabel'] ?? '',
            $data['checkboxLabel'] ?? '',
            $data['buttonLabel'] ?? ''
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        try {
            $formRendered = $this->handler->handle($dto);
            return new JsonResponse(['form' => $formRendered]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
