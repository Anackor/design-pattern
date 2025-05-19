<?php

namespace App\Domain\Flyweight;

/**
 * Flyweight Factory for managing and reusing UserType instances.
 *
 * This factory currently stores basic flyweight instances in memory, ensuring that the same UserType
 * string (e.g., "admin", "viewer", "editor") is only instantiated once and shared across users.
 *
 * ───────────────────────────────────────────────────────────────────────────────
 * If we were to evolve this factory to return actual UserRole entities (persisted objects from a database),
 * we would need to:
 *  - Inject a UserRoleRepository (e.g., Doctrine repository)
 *  - Replace the current in-memory `$instances` logic with a fetch-from-DB pattern
 *  - Optionally use a caching layer to avoid repetitive queries
 *
 * This way, the Flyweight would no longer be a simple string wrapper, but a lightweight manager of ORM-managed entities.
 */
class UserTypeFlyweightFactory
{
    /**
     * @var UserType[]
     */
    private array $types = [];

    public function getUserType(string $type): UserType
    {
        if (!isset($this->types[$type])) {
            $this->types[$type] = new UserType($type);
        }

        return $this->types[$type];
    }
}
