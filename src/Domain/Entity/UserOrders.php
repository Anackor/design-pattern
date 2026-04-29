<?php

namespace App\Domain\Entity;

use App\Shared\ValueObject\Money;

class UserOrders
{
    private ?int $id = null;

    private ?User $user = null;

    private ?string $totalPrice = null;

    private ?\DateTimeImmutable $createdAt = null;

    public static function placeFor(
        User $user,
        Money $totalPrice,
        ?\DateTimeImmutable $createdAt = null
    ): self {
        $order = new self();
        $order->setUser($user);
        $order->updateTotalPrice($totalPrice);
        $order->setCreatedAt($createdAt ?? new \DateTimeImmutable());

        return $order;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->getUser();
    }

    public function setUserId(?User $user_id): static
    {
        return $this->setUser($user_id);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalPrice(): string
    {
        if (null === $this->totalPrice) {
            throw new \LogicException('User order total price has not been initialized.');
        }

        return $this->totalPrice;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->totalPrice = Money::fromDecimalString($total_price)->toDecimalString();

        return $this;
    }

    public function getTotalPriceMoney(string $currency = 'EUR'): Money
    {
        return Money::fromDecimalString($this->getTotalPrice(), $currency);
    }

    public function updateTotalPrice(Money $totalPrice): static
    {
        $this->totalPrice = $totalPrice->toDecimalString();

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        if (null === $this->createdAt) {
            throw new \LogicException('User order creation date has not been initialized.');
        }

        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        if ($created_at > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('User order creation date cannot be in the future.');
        }

        $this->createdAt = $created_at;

        return $this;
    }
}
