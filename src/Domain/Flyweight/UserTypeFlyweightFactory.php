<?php

namespace App\Domain\Flyweight;

/**
 * Flyweight Factory responsible for creating and reusing shared {@see UserType} instances.
 *
 * In the Flyweight pattern, the factory is the entry point that prevents consumers from
 * instantiating duplicated objects when the same intrinsic state appears again and again.
 * In this example, that intrinsic state is the normalized user type string stored inside
 * {@see UserType}, such as "admin", "editor" or "viewer".
 *
 * Why this class matters in the pattern:
 * - It centralizes the lifecycle of flyweights instead of scattering `new UserType(...)`
 *   calls across services or entities.
 * - It guarantees that once a type has been normalized, the same shared instance is reused
 *   for subsequent requests with the same semantic value.
 * - It makes the optimization explicit and easy to explain in training sessions, code
 *   reviews and architectural discussions with teams.
 *
 * Important study note:
 * The Flyweight benefit does not come from the value object alone, but from combining an
 * immutable shared object with a factory that keeps an internal pool of previously created
 * instances. Without that pool, every caller would still create independent objects and we
 * would lose the memory-saving and identity-sharing aspects of the pattern.
 *
 * Practical tip for companies and teams:
 * Flyweight is especially useful when imports, integrations or batch processes repeat a
 * small catalog of values many times. If the repeated concept later becomes richer
 * (database-backed, translated, configurable or cached across requests), this factory is
 * also the natural place to evolve the retrieval strategy without changing every consumer.
 */
class UserTypeFlyweightFactory
{
    /**
     * Internal flyweight pool keyed by the normalized intrinsic state of the type.
     *
     * The array acts as an in-memory registry for the current lifecycle of the factory:
     * once a normalized type has been created, any future request for that same key returns
     * the already existing object instead of allocating a new one.
     *
     * @var UserType[]
     */
    private array $types = [];

    /**
     * Returns a shared flyweight for the provided user type.
     *
     * The input is first normalized by {@see UserType::fromString()}, which ensures the
     * factory never stores invalid or non-canonical values. The normalized type string is
     * then used as the cache key so that equivalent inputs like `" admin "` and `"admin"`
     * resolve to the same shared instance.
     *
     * This keeps the creation rule in one place and makes the Flyweight collaboration very
     * visible: callers ask for a type, while the factory decides whether to create it once
     * or return one that is already being shared.
     */
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
