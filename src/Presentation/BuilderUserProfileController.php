<?php

namespace App\Presentation;

use App\Application\DTO\UserProfileDTO;
use App\Application\BuilderUserProfile\CreateUserProfileHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * BuilderUserProfileController now demonstrates a small but explicit HTTP
 * contract: validate, delegate, then respond through the shared API factory.
 *
 * This makes the controller a teaching example for two concerns at once:
 * input mapping and response normalization.
 */
class BuilderUserProfileController
{
    public function __construct(
        private CreateUserProfileHandler $handler,
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/users/{id}/profile', methods: ['POST'])]
    public function createProfile(int $id, Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);
        $dto = new UserProfileDTO($id, $data['phone'] ?? null, $data['address'] ?? null, $data['birthdate'] ?? null);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        $profile = $this->handler->handle($dto);
        if (!$profile) {
            return $this->apiResponseFactory->httpError(Response::HTTP_NOT_FOUND, 'User not found');
        }

        return $this->apiResponseFactory->success(
            'Profile created',
            ['id' => $profile->getId()],
            Response::HTTP_CREATED
        );
    }
}
