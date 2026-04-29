<?php

namespace App\Presentation;

use App\Application\GetUser\GetUserQuery;
use App\Application\GetUser\GetUserHandler;
use App\Presentation\Http\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    public function __construct(
        private GetUserHandler $getUserHandler,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/users/{id}', methods: ['GET'])]
    public function getUser(int $id): JsonResponse
    {
        $query = new GetUserQuery($id);
        $user = $this->getUserHandler->handle($query);

        if (!$user) {
            return $this->apiResponseFactory->httpError(Response::HTTP_NOT_FOUND, 'User not found');
        }

        return $this->apiResponseFactory->success('User retrieved', [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}
