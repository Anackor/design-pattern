<?php

namespace App\Domain\Flyweight;

/**
 * Flyweight Factory responsible for reusing shared {@see Country} instances.
 *
 * This class mirrors the same Flyweight idea used for user types: if many imported users
 * belong to the same country, we do not need a brand new {@see Country} object for every
 * row. Instead, we keep one shared instance per normalized country name and hand it back
 * whenever the same intrinsic state is requested again.
 *
 * Educational note:
 * Factories like this one are useful to explain that Flyweight is not only about "small
 * objects", but about separating what is stable and repeatable from what is contextual.
 * The country name is stable intrinsic state; user-specific data remains outside the
 * flyweight and is therefore extrinsic state handled by the importing flow.
 */
class CountryFlyweightFactory
{
    /**
     * Internal pool of countries keyed by their normalized intrinsic state.
     *
     * @var Country[]
     */
    private array $countries = [];

    /**
     * Returns a shared country flyweight for the provided name.
     *
     * By normalizing first and caching second, the factory ensures it only stores canonical
     * values and keeps the sharing rule centralized in a single collaboration point.
     */
    public function getCountry(string $name): Country
    {
        $country = Country::fromName($name);
        $key = $country->getName();

        if (!isset($this->countries[$key])) {
            $this->countries[$key] = $country;
        }

        return $this->countries[$key];
    }
}
