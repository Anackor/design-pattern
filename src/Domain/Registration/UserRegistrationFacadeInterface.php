<?php

namespace App\Domain\Registration;

use App\Application\DTO\UserDataDTO;
use App\Domain\Entity\User;

interface UserRegistrationFacadeInterface
{
    public function register(UserDataDTO $userData): User;
}
