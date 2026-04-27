<?php

namespace App\Domain\Flyweight;

/**
 * Flyweight Factory for managing and reusing normalized UserType instances.
 */
class UserTypeFlyweightFactory
{
    /**
     * @var UserType[]
     */
    private array $types = [];

    public function getUserType(string $type): UserType
    {
        $userType = UserType::fromString($type);
        $key = $userType->getType();

        if (!isset($this->types[$key])) {
            $this->types[$key] = $userType;
        }

        return $this->types[$key];
    }
}
