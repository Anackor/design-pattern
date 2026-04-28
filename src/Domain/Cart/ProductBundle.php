<?php

namespace App\Domain\Cart;

use App\Shared\ValueObject\Money;

/**
 * ProductBundle is the "Composite" in the Composite pattern.
 * It can contain multiple products or even other bundles.
 */
class ProductBundle implements ProductInterface
{
    /** @var ProductInterface[] */
    private array $items = [];

    public function __construct(private string $name) {}

    public function add(ProductInterface $product): void
    {
        $this->items[] = $product;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return array_reduce(
            $this->items,
            fn(Money $total, ProductInterface $item) => $total->add($item->getPrice()),
            Money::zero()
        );
    }
}
