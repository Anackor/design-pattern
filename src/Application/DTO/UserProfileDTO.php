<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserProfileDTO
{
    /**
     * Validation phone number directly in the DTO ensures that:
     * - The validation logic is centralized, avoiding duplication in controllers or services
     * - Requests with invalid data are rejected early
     * - Errors can be automatically handled by Symfony's validation system
     */
    public function __construct(
        #[Assert\NotBlank]
        public int $userId,
        #[Assert\NotBlank(message: 'Phone cannot be empty.')]
        #[Assert\Length(min: 9, max: 15)]
        public ?string $phone = null,
        #[Assert\NotBlank(message: 'Address cannot be empty.')]
        #[Assert\Length(min: 5)]
        public ?string $address = null,
        #[Assert\NotBlank(message: 'Birthdate cannot be empty.')]
        #[Assert\Date]
        public ?string $birthdate = null
    ) {}
}
