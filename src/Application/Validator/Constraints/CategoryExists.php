<?php

namespace App\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class CategoryExists extends Constraint
{
    public string $message = 'The category with id "{{ id }}" does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
