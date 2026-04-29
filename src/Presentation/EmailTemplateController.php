<?php

namespace App\Presentation;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Template\RenderEmailTemplateHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EmailTemplateController
{
    public function __construct(
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/api/render-template', methods: ['POST'])]
    public function render(Request $request, RenderEmailTemplateHandler $handler): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['templateKey'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $dto = new RenderTemplateDTO(
            $data['templateKey'],
            $data['payload'] ?? []
        );

        try {
            $content = $handler->handle($dto);
            return $this->apiResponseFactory->success('Template rendered', ['content' => $content]);
        } catch (\Exception $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
