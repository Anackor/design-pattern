<?php

namespace App\Domain\Repository;

use App\Domain\Entity\UserProfile;

interface UserProfileRepositoryInterface
{
    public function profileOfId(int $profileId): ?UserProfile;

    public function addProfile(UserProfile $profile): void;
}
