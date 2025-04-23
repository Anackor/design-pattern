<?php

namespace App\Presentation;

use App\Application\DTO\RenderTemplateDTO;
use App\Application\Template\RenderEmailTemplateHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmailTemplateController
{
    #[Route('/api/render-template', methods: ['POST'])]
    public function render(Request $request, RenderEmailTemplateHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new RenderTemplateDTO(
            $data['templateKey'] ?? '',
            $data['payload'] ?? []
        );

        try {
            $content = $handler->handle($dto);
            return new JsonResponse(['content' => $content]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
