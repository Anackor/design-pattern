<?php

namespace App\Tests\Unit\Application\Validator\Constraints;

use App\Application\Validator\Constraints\CategoryExists;
use App\Application\Validator\Constraints\CategoryExistsValidator;
use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CategoryExistsValidatorTest extends ConstraintValidatorTestCase
{
    private CategoryRepositoryInterface&MockObject $categoryRepository;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        parent::setUp();
    }

    public function testConstraintTargetsProperties(): void
    {
        $constraint = new CategoryExists();

        $this->assertSame(Constraint::PROPERTY_CONSTRAINT, $constraint->getTargets());
    }

    public function testValidateIgnoresNullValues(): void
    {
        $this->validator->validate(null, new CategoryExists());

        $this->assertNoViolation();
    }

    public function testValidatePassesWhenCategoryExists(): void
    {
        $this->categoryRepository->expects($this->once())
            ->method('catalogCategoryOfId')
            ->with(5)
            ->willReturn(Category::named('Office'));

        $this->validator->validate(5, new CategoryExists());

        $this->assertNoViolation();
    }

    public function testValidateBuildsViolationWhenCategoryDoesNotExist(): void
    {
        $this->categoryRepository->expects($this->once())
            ->method('catalogCategoryOfId')
            ->with(99)
            ->willReturn(null);

        $constraint = new CategoryExists();

        $this->validator->validate(99, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ id }}', '99')
            ->assertRaised();
    }

    public function testValidateRejectsUnexpectedConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(10, new class extends Constraint {});
    }

    protected function createValidator(): CategoryExistsValidator
    {
        return new CategoryExistsValidator($this->categoryRepository);
    }
}
