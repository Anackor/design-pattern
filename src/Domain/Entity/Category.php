<?php

namespace App\Domain\Entity;

class Category
{
    private ?int $id = null;

    private ?string $name = null;

    /**
     * @var iterable<int, Product>
     */
    private iterable $products;

    public function __construct()
    {
        $this->products = [];
    }

    public static function named(string $name): self
    {
        $category = new self();
        $category->setName($name);

        return $category;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $normalized = trim($name);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('Category name cannot be empty.');
        }

        $this->name = $normalized;

        return $this;
    }

    /**
     * @return list<Product>
     */
    public function getProducts(): array
    {
        return $this->iterableToArray($this->products);
    }

    public function addProduct(Product $product): static
    {
        if (!$this->containsProduct($product)) {
            $this->products = $this->appendProduct($this->products, $product);
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->containsProduct($product)) {
            $this->products = $this->withoutProduct($this->products, $product);

            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    private function containsProduct(Product $candidate): bool
    {
        foreach ($this->products as $product) {
            if ($product === $candidate) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param iterable<int, Product> $products
     */
    private function appendProduct(iterable $products, Product $product): iterable
    {
        if (is_object($products) && method_exists($products, 'add')) {
            $products->add($product);

            return $products;
        }

        $buffer = $this->iterableToArray($products);
        $buffer[] = $product;

        return $buffer;
    }

    /**
     * @param iterable<int, Product> $products
     */
    private function withoutProduct(iterable $products, Product $candidate): iterable
    {
        if (is_object($products) && method_exists($products, 'removeElement')) {
            $products->removeElement($candidate);

            return $products;
        }

        return array_values(array_filter(
            $this->iterableToArray($products),
            static fn(Product $product): bool => $product !== $candidate
        ));
    }

    /**
     * @template T of object
     *
     * @param iterable<int, T> $items
     *
     * @return list<T>
     */
    private function iterableToArray(iterable $items): array
    {
        if (is_array($items)) {
            return array_values($items);
        }

        return array_values(iterator_to_array($items, false));
    }
}
