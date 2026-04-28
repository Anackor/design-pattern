<?php

namespace App\Tests\Unit\Domain\Flyweight;

use App\Domain\Flyweight\CountryFlyweightFactory;
use App\Domain\Flyweight\UserTypeFlyweightFactory;
use PHPUnit\Framework\TestCase;

class FlyweightFactoryTest extends TestCase
{
    public function testCountryFactoryReusesNormalizedCountry(): void
    {
        $factory = new CountryFlyweightFactory();

        $left = $factory->getCountry('Spain');
        $right = $factory->getCountry(' Spain ');

        $this->assertSame($left, $right);
    }

    public function testUserTypeFactoryReusesNormalizedUserType(): void
    {
        $factory = new UserTypeFlyweightFactory();

        $left = $factory->getUserType('standard');
        $right = $factory->getUserType(' standard ');

        $this->assertSame($left, $right);
    }
}
