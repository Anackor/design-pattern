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
        if (!isset($this->countries[$name])) {
            $this->countries[$name] = new Country($name);
        }

        return $this->countries[$name];
    }
}
