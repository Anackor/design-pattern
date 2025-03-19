<?php

namespace App\Application\Service;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;

class UserService
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(string $name, string $email): User
    {
        $user = (new User())
            ->setName($name)
            ->setEmail($email);

        $this->userRepository->save($user);

        return $user;
    }
}
