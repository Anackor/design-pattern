<?php

namespace App\Presentation;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NotificationController
{
    private SendNotificationHandler $sendNotificationHandler;
    private ValidatorInterface $validator;

    public function __construct(SendNotificationHandler $sendNotificationHandler, ValidatorInterface $validator)
    {
        $this->sendNotificationHandler = $sendNotificationHandler;
        $this->validator = $validator;
    }


    #[Route('/send-notification', methods: ['POST'])]
    public function sendNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['receiver'], $data['message'], $data['channel'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $notificationDTO = new NotificationRequestDTO(
            $data['title'],
            $data['receiver'],
            $data['message'],
            $data['channel']
        );

        $errors = $this->validator->validate($notificationDTO);
        if (count($errors) > 0) {
            return new JsonResponse([
                'error' => 'Validation failed',
                'details' => (string) $errors,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $this->sendNotificationHandler->handle($notificationDTO);
            return new JsonResponse(['message' => 'Notification sent successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
