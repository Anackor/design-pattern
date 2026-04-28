<?php

namespace App\Domain\Builder;

use App\Domain\Entity\User;
use App\Domain\Entity\UserProfile;

class UserProfileBuilder
{
    private ?User $user = null;
    private ?string $phone = null;
    private ?string $address = null;
    private ?\DateTimeImmutable $birthdate = null;

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function setBirthdate(string|\DateTimeInterface $birthdate): self
    {
        if ($birthdate instanceof \DateTimeInterface) {
            $this->birthdate = \DateTimeImmutable::createFromInterface($birthdate);

            return $this;
        }

        $this->birthdate = $this->parseBirthdate($birthdate);

        return $this;
    }

    public function build(): UserProfile
    {
        if (!$this->user instanceof User) {
            throw new \LogicException('User profile builder requires a user.');
        }

        if (!is_string($this->phone)) {
            throw new \LogicException('User profile builder requires a phone number.');
        }

        if (!is_string($this->address)) {
            throw new \LogicException('User profile builder requires an address.');
        }

        if (!$this->birthdate instanceof \DateTimeImmutable) {
            throw new \LogicException('User profile builder requires a birth date.');
        }

        return new UserProfile($this->user, $this->phone, $this->address, $this->birthdate);
    }

    private function parseBirthdate(string $birthdate): \DateTimeImmutable
    {
        $normalized = trim($birthdate);

        if ('' === $normalized) {
            throw new \InvalidArgumentException('User profile birth date cannot be empty.');
        }

        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $normalized);
        $errors = \DateTimeImmutable::getLastErrors();

        if (!$parsed instanceof \DateTimeImmutable || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new \InvalidArgumentException('User profile birth date must use Y-m-d format.');
        }

        return $parsed;
    }
}
