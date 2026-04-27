<?php

namespace App\Application\Validator\Constraints;

use App\Domain\Repository\CategoryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CategoryExistsValidator extends ConstraintValidator
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CategoryExists) {
            throw new UnexpectedTypeException($constraint, CategoryExists::class);
        }

        if ($value === null) {
            return; // categoryId is nullable
        }

        if (!$this->categoryRepository->catalogCategoryOfId($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
        }
    }
}
