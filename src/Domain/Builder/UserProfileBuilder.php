<?php

namespace App\Domain\Builder;

use App\Domain\Entity\User;
use App\Domain\Entity\UserProfile;

class UserProfileBuilder
{
    private ?User $user = null;
    private ?string $phone = null;
    private ?string $address = null;
    private ?\DateTime $birthdate = null;

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

    public function setBirthdate(string $birthdate): self
    {
        $this->birthdate = new \DateTime($birthdate);
        return $this;
    }

    public function build(): UserProfile
    {
        return new UserProfile($this->user, $this->phone, $this->address, $this->birthdate);
    }
}
