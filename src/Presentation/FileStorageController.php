<?php

namespace App\Presentation;

use App\Application\DTO\FileOperationRequestDTO;
use App\Application\File\UploadFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\DeleteFileHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileStorageController
{
    public function __construct(
        private UploadFileHandler $uploadHandler,
        private DownloadFileHandler $downloadHandler,
        private DeleteFileHandler $deleteHandler,
        private ValidatorInterface $validator
    ) {}

    /**
     * Reusable helper to validate Data Transfer Objects using Symfony's Validator component.
     * By centralizing the validation logic, we avoid code duplication across controllers, 
     * ensure consistent error handling, and improve maintainability.
     */
    private function validateDto(object $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $messages));
        }
    }
    
    #[Route('/file/upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['adapter'], $data['path'], $data['contents'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new FileOperationRequestDTO($data['adapter'], $data['path'], $data['contents']);
        $this->validateDto($dto);
        $this->uploadHandler->handle($dto);

        return new JsonResponse(['message' => 'File uploaded successfully']);
    }

    #[Route('/file/download', methods: ['GET'])]
    public function download(Request $request): JsonResponse
    {
        $adapter = $request->query->get('adapter');
        $path = $request->query->get('path');

        if (!$adapter || !$path) {
            throw new BadRequestHttpException('Missing required query parameters.');
        }

        $dto = new FileOperationRequestDTO($adapter, $path);
        $this->validateDto($dto);

        $contents = $this->downloadHandler->handle($dto);

        return new JsonResponse(['contents' => $contents]);
    }

    #[Route('/file/delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['adapter'], $data['path'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new FileOperationRequestDTO($data['adapter'], $data['path']);
        $this->validateDto($dto);

        $this->deleteHandler->handle($dto);

        return new JsonResponse(['message' => 'File deleted successfully']);
    }
}
