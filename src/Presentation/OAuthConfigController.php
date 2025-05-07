<?php

namespace App\Presentation;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Application\DTO\GetOAuthConfigDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OAuthConfigController
{
    private GetOAuthConfigHandler $getOAuthConfigHandler;
    private ValidatorInterface $validator;

    public function __construct(GetOAuthConfigHandler $getOAuthConfigHandler, ValidatorInterface $validator)
    {
        $this->getOAuthConfigHandler = $getOAuthConfigHandler;
        $this->validator = $validator;
    }

    #[Route('/auth/config', methods: ['GET'])]
    public function getOAuthConfig(Request $request): JsonResponse
    {
        $provider = $request->query->get('provider');

        if (!$provider) {
            throw new BadRequestHttpException('Missing required "provider" parameter.');
        }

        $dto = new GetOAuthConfigDTO($provider);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'details' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $config = $this->getOAuthConfigHandler->handle($dto);
            return new JsonResponse([
                'client_id' => $config->clientId,
                'redirect_uri' => $config->redirectUri,
                'scopes' => $config->scopes,
            ], JsonResponse::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Unexpected error.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
