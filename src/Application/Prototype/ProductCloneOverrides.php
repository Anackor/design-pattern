<?php

namespace App\Application\Prototype;

use App\Domain\Entity\Category;
use App\Shared\ValueObject\Money;

final readonly class ProductCloneOverrides
{
    private function __construct(
        private ?string $name,
        private ?Money $price,
        private ?string $description,
        private ?Category $category,
        private bool $categoryProvided
    ) {}

    public static function fromScalars(
        ?string $name = null,
        ?float $price = null,
        ?string $description = null,
        ?Category $category = null,
        bool $categoryProvided = false
    ): self {
        return new self(
            $name,
            null === $price ? null : Money::fromFloat($price),
            $description,
            $category,
            $categoryProvided
        );
    }

    public function hasName(): bool
    {
        return null !== $this->name;
    }

    public function name(): string
    {
        if (null === $this->name) {
            throw new \LogicException('Product clone name override has not been provided.');
        }

        return $this->name;
    }

    public function hasPrice(): bool
    {
        return $this->price instanceof Money;
    }

    public function price(): Money
    {
        if (!$this->price instanceof Money) {
            throw new \LogicException('Product clone price override has not been provided.');
        }

        return $this->price;
    }

    public function hasDescription(): bool
    {
        return null !== $this->description;
    }

    public function description(): string
    {
        if (null === $this->description) {
            throw new \LogicException('Product clone description override has not been provided.');
        }

        return $this->description;
    }

    public function categoryWasProvided(): bool
    {
        return $this->categoryProvided;
    }

    public function category(): ?Category
    {
        return $this->category;
    }
}
