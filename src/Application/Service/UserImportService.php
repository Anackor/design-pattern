<?php

namespace App\Application\Service;

use App\Domain\Entity\User;
use App\Domain\Enum\UserRole;
use App\Domain\Flyweight\CountryFlyweightFactory;
use App\Domain\Flyweight\UserTypeFlyweightFactory;

/**
 * This service demonstrates the use of the Flyweight Pattern to efficiently import a large volume of users.
 *
 * The Flyweight Pattern is used here to reduce memory usage and object creation overhead by sharing common,
 * immutable data — such as user types and countries — across many user instances.
 *
 * Instead of creating a new Country or UserType object for each user, the service retrieves them from their
 * respective Flyweight factories, ensuring that identical values share the same instance.
 *
 * This greatly improves performance, particularly when processing large datasets with repetitive values.
 */
class UserImportService
{
    public function __construct(
        private CountryFlyweightFactory $countryFactory,
        private UserTypeFlyweightFactory $userTypeFactory,
    ) {}

    /**
     * @return User[]
     */
    public function importFromArray(array $rawUsers): array
    {
        $users = [];

        foreach ($rawUsers as $data) {
            $country = $this->countryFactory->getCountry($data['country']);
            $userType = $this->userTypeFactory->getUserType($data['type']);

            $user = User::register(
                $data['name'],
                $data['email'],
                UserRole::USER,
                $country->getName(),
                $userType->getType()
            );

            $users[] = $user;
        }

        return $users;
    }
}
