<?php

namespace App\Tests\Unit\Domain\Flyweight;

use App\Domain\Flyweight\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testFromNameNormalizesCountryName(): void
    {
        $country = Country::fromName(' Spain ');

        $this->assertSame('Spain', $country->getName());
    }

    public function testFromNameRejectsEmptyCountry(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Country name cannot be empty.');

        Country::fromName('   ');
    }

    public function testEqualsComparesName(): void
    {
        $left = Country::fromName('Spain');
        $right = Country::fromName('Spain');

        $this->assertTrue($left->equals($right));
    }
}
