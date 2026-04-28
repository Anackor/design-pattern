<?php

namespace App\Domain\Entity;

use App\Shared\ValueObject\Money;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

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
        return $this->user_id;
    }

    public function setUser(?User $user): static
    {
        $this->user_id = $user;

        return $this;
    }

    public function getTotalPrice(): string
    {
        if (null === $this->total_price) {
            throw new \LogicException('User order total price has not been initialized.');
        }

        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->total_price = Money::fromDecimalString($total_price)->toDecimalString();

        return $this;
    }

    public function getTotalPriceMoney(string $currency = 'EUR'): Money
    {
        return Money::fromDecimalString($this->getTotalPrice(), $currency);
    }

    public function updateTotalPrice(Money $totalPrice): static
    {
        $this->total_price = $totalPrice->toDecimalString();

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        if (null === $this->created_at) {
            throw new \LogicException('User order creation date has not been initialized.');
        }

        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        if ($created_at > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('User order creation date cannot be in the future.');
        }

        $this->created_at = $created_at;

        return $this;
    }
}
