<?php

namespace App\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_of_birth = null;

    public function __construct(User $user, string $phoneNumber, string $address, \DateTimeInterface $birthDate)
    {
        $this->setUser($user);
        $this->setPhone($phoneNumber);
        $this->setAddress($address);
        $this->setDateOfBirth($birthDate);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $normalized = trim($address);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('User profile address cannot be empty.');
        }

        $this->address = $normalized;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $normalized = preg_replace('/\s+/', '', trim($phone));

        if (!is_string($normalized) || !preg_match('/^\+?[0-9]{9,15}$/', $normalized)) {
            throw new \InvalidArgumentException('User profile phone must contain 9 to 15 digits.');
        }

        $this->phone = $normalized;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(\DateTimeInterface $date_of_birth): static
    {
        if ($date_of_birth > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('User profile birth date cannot be in the future.');
        }

        $this->date_of_birth = $date_of_birth;

        return $this;
    }
}
