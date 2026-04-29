<?php

namespace App\Presentation;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Presentation\Http\ApiResponseFactory;
use App\Presentation\Http\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * NotificationController is a good candidate for a shared HTTP contract because
 * it mixes validation, client mistakes and outbound failures.
 *
 * By delegating response creation to ApiResponseFactory, we keep those
 * branches visible while making the payload shape consistent across the API.
 */
class NotificationController
{
    public function __construct(
        private SendNotificationHandler $sendNotificationHandler,
        private ValidatorInterface $validator,
        private JsonRequestDecoder $jsonRequestDecoder,
        private ApiResponseFactory $apiResponseFactory
    ) {}

    #[Route('/send-notification', methods: ['POST'])]
    public function sendNotification(Request $request): JsonResponse
    {
        $data = $this->jsonRequestDecoder->decodeObject($request);

        if (!isset($data['title'], $data['receiver'], $data['message'], $data['channel'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $notificationDTO = new NotificationRequestDTO(
            $data['title'],
            $data['message'],
            $data['receiver'],
            $data['channel']
        );

        $errors = $this->validator->validate($notificationDTO);
        if (count($errors) > 0) {
            return $this->apiResponseFactory->validationError($errors);
        }

        try {
            $this->sendNotificationHandler->handle($notificationDTO);
            return $this->apiResponseFactory->success('Notification sent successfully');
        } catch (\Exception $e) {
            return $this->apiResponseFactory->httpError(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
