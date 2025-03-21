<?php

namespace App\Application\GetUser;

use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Entity\User;
use Symfony\Component\Uid\UuidV4;

class GetUserHandler
{
    public function __construct(private UserRepository $userRepository) {}

    public function handle(GetUserQuery $query): ?User
    {
        return $this->userRepository->findById($query->userId);
    }
}
