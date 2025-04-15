<?php

namespace App\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CategoryExists extends Constraint
{
    public string $message = 'The category with id "{{ id }}" does not exist.';
}
