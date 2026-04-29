<?php

namespace App\Presentation;

use App\Application\DTO\CreateDocumentDTO;
use App\Application\DTO\UpdateDocumentDTO;
use App\Application\Document\CreateDocumentHandler;
use App\Application\Document\UpdateDocumentContentHandler;
use App\Domain\Entity\Document;
use App\Domain\Entity\DocumentVersion;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentController
{
    public function __construct(
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/documents', name: 'document_create', methods: ['POST'])]
    public function create(
        Request $request,
        CreateDocumentHandler $handler
    ): JsonResponse {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['title'], $data['content'], $data['userID'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new CreateDocumentDTO(
            $data['title'],
            $data['content'],
            $data['userID']
        );


        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $document = $handler->handle($dto);
            return $this->apiResponseFactory->success(
                'Document created',
                ['document' => $this->serializeDocument($document)],
                Response::HTTP_CREATED
            );
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    #[Route('/documents/{id}/content', name: 'document_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        UpdateDocumentContentHandler $handler
    ): JsonResponse {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['content'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new UpdateDocumentDTO(
            $id,
            $data['content']
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $version = $handler->handle($dto);
            return $this->apiResponseFactory->success('Document content updated', [
                'version' => $this->serializeVersion($version),
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * The controller exposes a transport-friendly snapshot instead of the raw
     * Doctrine entity so the HTTP contract stays explicit and stable.
     *
     * @return array{id: ?int, title: ?string, version_count: int}
     */
    private function serializeDocument(Document $document): array
    {
        return [
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'version_count' => $document->getVersions()->count(),
        ];
    }

    /**
     * Returning a summarized version payload keeps the response useful without
     * coupling callers to the full internal shape of the entity.
     *
     * @return array{id: ?int, document_id: ?int, version_code: string, created_at: string}
     */
    private function serializeVersion(DocumentVersion $version): array
    {
        return [
            'id' => $version->getId(),
            'document_id' => $version->getDocument()->getId(),
            'version_code' => $version->getVersionCode(),
            'created_at' => $version->getCreatedAt()->format(\DATE_ATOM),
        ];
    }
}
