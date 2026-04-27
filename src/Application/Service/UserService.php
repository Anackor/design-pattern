<?php

namespace App\Application\Service;

use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Entity\User;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function createUser(string $name, string $email): User
    {
        $user = User::register($name, $email);

        $this->userRepository->addRegisteredUser($user);

        return $user;
    }
}
