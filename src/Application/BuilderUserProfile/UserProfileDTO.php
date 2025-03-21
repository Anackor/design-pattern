<?php

namespace App\Application\BuilderUserProfile;

use Symfony\Component\Validator\Constraints as Assert;

class UserProfileDTO
{
    public function __construct(
        #[Assert\NotBlank] public int $userId,
        #[Assert\Length(min: 9, max: 15)] public ?string $phone = null,
        #[Assert\Length(min: 5)] public ?string $address = null,
        #[Assert\Date] public ?string $birthdate = null
    ) {}
}
