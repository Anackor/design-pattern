<?php

namespace App\Application\BuilderUserProfile;

use App\Application\DTO\UserProfileDTO;
use App\Domain\Builder\UserProfileBuilder;
use App\Domain\Repository\UserRepositoryInterface as UserRepository;
use App\Domain\Repository\UserProfileRepositoryInterface as UserProfileRepository;

class CreateUserProfileHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserProfileRepository $userProfileRepository
    ) {}

    public function handle(UserProfileDTO $dto)
    {
        $user = $this->userRepository->registeredUserOfId($dto->userId);
        if (!$user) {
            return null;
        }

        if (null === $dto->phone || null === $dto->address || null === $dto->birthdate) {
            throw new \InvalidArgumentException('User profile data is incomplete.');
        }

        $profile = (new UserProfileBuilder())
            ->setUser($user)
            ->setPhone($dto->phone)
            ->setAddress($dto->address)
            ->setBirthdate($dto->birthdate)
            ->build();

        $this->userProfileRepository->addProfile($profile);
        return $profile;
    }
}
