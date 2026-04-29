<?php

namespace App\Presentation\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * JsonRequestDecoder centralizes one of the most repeated concerns in this API:
 * reading a request body and turning it into a predictable PHP structure.
 *
 * Keeping this logic in one reusable class is useful for two reasons:
 * - the controllers stay focused on mapping input to DTOs and delegating work;
 * - malformed JSON now fails with the same type of client-facing error instead
 *   of surfacing as warnings, type errors or half-decoded payloads.
 *
 * The decoder intentionally accepts only JSON objects because the current HTTP
 * endpoints model named fields rather than positional arrays.
 */
final class JsonRequestDecoder
{
    /**
     * @return array<string, mixed>
     */
    public function decodeObject(Request $request): array
    {
        $content = trim($request->getContent());

        if ('' === $content) {
            return [];
        }

        try {
            $decoded = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BadRequestHttpException('Invalid JSON payload.', $exception);
        }

        if ($decoded instanceof \stdClass) {
            /** @var array<string, mixed> $data */
            $data = $this->normalizeObject($decoded);

            return $data;
        }

        throw new BadRequestHttpException('Request body must be a JSON object.');
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeObject(\stdClass $object): array
    {
        $normalized = [];

        foreach (get_object_vars($object) as $key => $value) {
            $normalized[$key] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof \stdClass) {
            return $this->normalizeObject($value);
        }

        if (is_array($value)) {
            return array_map($this->normalizeValue(...), $value);
        }

        return $value;
    }
}
