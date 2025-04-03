<?php

namespace App\Presentation;

use App\Application\GetUser\GetUserQuery;
use App\Application\GetUser\GetUserHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    public function __construct(private GetUserHandler $getUserHandler) {}

    #[Route('/users/{id}', methods: ['GET'])]
    public function getUser(int $id): JsonResponse
    {
        $query = new GetUserQuery($id);
        $user = $this->getUserHandler->handle($query);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}
