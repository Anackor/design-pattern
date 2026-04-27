<?php

namespace App\Domain\Flyweight;

class CountryFlyweightFactory
{
    /**
     * @var Country[]
     */
    private array $countries = [];

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
