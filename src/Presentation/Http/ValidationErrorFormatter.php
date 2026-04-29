<?php

namespace App\Presentation\Http;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * ValidationErrorFormatter converts Symfony validation objects into a payload
 * that is easier to expose through an HTTP API.
 *
 * This translation is intentionally explicit because the default string cast of
 * a violation list is useful for debugging, but unstable and awkward as a
 * public contract. By projecting each violation into `{field, message}`, the
 * API becomes easier to consume and the tests can assert behavior without
 * depending on framework-specific formatting details.
 */
final class ValidationErrorFormatter
{
    /**
     * @return list<array{field: string, message: string}>
     */
    public function format(ConstraintViolationListInterface $violations): array
    {
        $formatted = [];

        foreach ($violations as $violation) {
            $formatted[] = $this->formatViolation($violation);
        }

        return $formatted;
    }

    /**
     * @return array{field: string, message: string}
     */
    private function formatViolation(ConstraintViolationInterface $violation): array
    {
        $field = trim($violation->getPropertyPath());

        return [
            'field' => '' !== $field ? $field : 'payload',
            'message' => $violation->getMessage(),
        ];
    }
}
