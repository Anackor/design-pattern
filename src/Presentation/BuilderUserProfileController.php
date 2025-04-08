<?php

namespace App\Presentation;

use App\Application\DTO\UserProfileDTO;
use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BuilderUserProfileController
{
    public function __construct(
        private CreateUserProfileHandler $handler,
        private ValidatorInterface $validator
    ) {}

    #[Route('/users/{id}/profile', methods: ['POST'])]
    public function createProfile(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new UserProfileDTO($id, $data['phone'] ?? null, $data['address'] ?? null, $data['birthdate'] ?? null);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        $profile = $this->handler->handle($dto);
        if (!$profile) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse(['message' => 'Profile created', 'id' => $profile->getId()], 201);
    }
}
