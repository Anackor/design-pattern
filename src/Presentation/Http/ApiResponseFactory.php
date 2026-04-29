<?php

namespace App\Presentation\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * ApiResponseFactory defines the HTTP envelope we want to teach and preserve
 * across the API surface.
 *
 * The contract is deliberately small:
 * - every response declares a top-level `status`;
 * - successful responses expose a human-friendly `message` and a `data` block;
 * - failed responses expose the same `message` plus an `error` block with a
 *   stable machine-friendly `type` and optional `details`.
 *
 * Keeping that policy in one service lets the controllers focus on orchestration
 * while making the transport contract explicit, testable and easy to extend.
 */
final class ApiResponseFactory
{
    public function __construct(private ValidationErrorFormatter $validationErrorFormatter) {}

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function success(
        string $message,
        array $data = [],
        int $statusCode = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        if ([] !== $meta) {
            $payload['meta'] = $meta;
        }

        return new JsonResponse($payload, $statusCode);
    }

    /**
     * @param array<string, mixed>|list<array{field: string, message: string}> $details
     */
    public function error(
        string $message,
        string $type,
        int $statusCode,
        array $details = []
    ): JsonResponse {
        $error = ['type' => $type];

        if ([] !== $details) {
            $error['details'] = $details;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'error' => $error,
        ], $statusCode);
    }

    public function validationError(
        ConstraintViolationListInterface $violations,
        string $message = 'Validation failed'
    ): JsonResponse {
        return $this->error(
            $message,
            'validation_failed',
            Response::HTTP_BAD_REQUEST,
            $this->validationErrorFormatter->format($violations)
        );
    }

    /**
     * @param array<string, mixed>|list<array{field: string, message: string}> $details
     */
    public function httpError(int $statusCode, string $message, array $details = []): JsonResponse
    {
        return $this->error($message, $this->typeFromStatusCode($statusCode), $statusCode, $details);
    }

    private function typeFromStatusCode(int $statusCode): string
    {
        return match ($statusCode) {
            Response::HTTP_BAD_REQUEST => 'bad_request',
            Response::HTTP_NOT_FOUND => 'not_found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'method_not_allowed',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'unprocessable_entity',
            Response::HTTP_INTERNAL_SERVER_ERROR => 'internal_server_error',
            default => 'http_error',
        };
    }
}
