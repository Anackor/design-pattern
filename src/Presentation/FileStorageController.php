<?php

namespace App\Presentation;

use App\Application\DTO\FileOperationRequestDTO;
use App\Application\File\UploadFileHandler;
use App\Application\File\DownloadFileHandler;
use App\Application\File\DeleteFileHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileStorageController
{
    public function __construct(
        private UploadFileHandler $uploadHandler,
        private DownloadFileHandler $downloadHandler,
        private DeleteFileHandler $deleteHandler,
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    /**
     * Reusable helper to validate Data Transfer Objects using Symfony's Validator component.
     * By centralizing the validation logic, we avoid code duplication across controllers,
     * ensure consistent error handling, and improve maintainability.
     */
    private function validateDto(object $dto): ConstraintViolationListInterface
    {
        return $this->validator->validate($dto);
    }

    #[Route('/file/upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['adapter'], $data['path'], $data['contents'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new FileOperationRequestDTO($data['adapter'], $data['path'], $data['contents']);
        $errors = $this->validateDto($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        $this->uploadHandler->handle($dto);

        return $this->apiResponseFactory->success('File uploaded successfully');
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
        $errors = $this->validateDto($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        $contents = $this->downloadHandler->handle($dto);

        return $this->apiResponseFactory->success('File downloaded successfully', ['contents' => $contents]);
    }

    #[Route('/file/delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['adapter'], $data['path'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new FileOperationRequestDTO($data['adapter'], $data['path']);
        $errors = $this->validateDto($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        $this->deleteHandler->handle($dto);

        return $this->apiResponseFactory->success('File deleted successfully');
    }
}
