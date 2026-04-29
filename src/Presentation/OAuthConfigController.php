<?php

namespace App\Presentation;

use App\Application\Auth\GetOAuthConfigHandler;
use App\Application\DTO\GetOAuthConfigDTO;
use App\Presentation\Http\ApiResponseFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OAuthConfigController
{
    private GetOAuthConfigHandler $getOAuthConfigHandler;
    private ValidatorInterface $validator;

    public function __construct(
        GetOAuthConfigHandler $getOAuthConfigHandler,
        ValidatorInterface $validator,
        private ApiResponseFactory $apiResponseFactory
    ) {
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
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $config = $this->getOAuthConfigHandler->handle($dto);
            return $this->apiResponseFactory->success('OAuth configuration loaded', [
                'client_id' => $config->clientId,
                'redirect_uri' => $config->redirectUri,
                'scopes' => $config->scopes,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (\Throwable $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unexpected error.');
        }
    }
}
