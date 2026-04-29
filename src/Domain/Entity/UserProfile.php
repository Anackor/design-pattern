<?php

namespace App\Domain\Entity;

class UserProfile
{
    private ?int $id = null;

    private ?User $user = null;

    private ?string $address = null;

    private ?string $phone = null;

    private ?\DateTimeInterface $dateOfBirth = null;

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
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $date_of_birth): static
    {
        if ($date_of_birth > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('User profile birth date cannot be in the future.');
        }

        $this->dateOfBirth = $date_of_birth;

        return $this;
    }
}
