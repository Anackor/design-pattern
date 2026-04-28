<?php

namespace App\Domain\Entity;

use App\Shared\ValueObject\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private float $price;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    public function __construct(
        string $name,
        float $price,
        string $description,
        Category $category
    ) {
        $this->setName($name);
        $this->setPrice($price);
        $this->setDescription($description);
        $this->setCategory($category);
    }

    public function clone(): self
    {
        return new self(
            $this->name,
            $this->getPriceMoney()->toFloat(),
            $this->description,
            $this->category
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $normalized = trim($name);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Product name cannot be empty.');
        }

        $this->name = $normalized;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceMoney(string $currency = 'EUR'): Money
    {
        return Money::fromFloat($this->price, $currency);
    }

    public function setPrice(float $price): static
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative.');
        }

        $this->updatePrice(Money::fromFloat($price));

        return $this;
    }

    public function updatePrice(Money $price): static
    {
        $this->price = $price->toFloat();

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $normalized = trim($description);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Product description cannot be empty.');
        }

        $this->description = $normalized;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
