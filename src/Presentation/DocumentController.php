<?php

namespace App\Presentation;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\DTO\UpdateDocumentDTO;
use App\Application\Document\CreateDocumentHandler;
use App\Application\Document\UpdateDocumentContentHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentController
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    #[Route('/documents', name: 'document_create', methods: ['POST'])]
    public function create(
        Request $request,
        CreateDocumentHandler $handler
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        $dto = new CreateDocumentDTO(
            $data['title'],
            $data['content'],
            $data['userID']
        );
    
        
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        try {
            $document = $handler->handle($dto);
            return new JsonResponse(['document' => $document]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/documents/{id}/content', name: 'document_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        UpdateDocumentContentHandler $handler
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new UpdateDocumentDTO(
            $id,
            $data['content']
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        try {
            $version = $handler->handle($dto);
            return new JsonResponse(['version' => $version]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
