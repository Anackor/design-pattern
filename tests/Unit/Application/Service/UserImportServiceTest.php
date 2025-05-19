<?php

namespace App\Tests\Application\Service;

use App\Application\Service\UserImportService;
use App\Domain\Flyweight\CountryFlyweightFactory;
use App\Domain\Flyweight\UserTypeFlyweightFactory;
use PHPUnit\Framework\TestCase;

class UserImportServiceTest extends TestCase
{
    public function testItImportsUsersCorrectlyFromCSV(): void
    {
        $csvPath = dirname(__DIR__, 4) . '/resources/csv/users_template.csv';

        $rows = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($rows);

        $data = array_map(function ($row) use ($headers) {
            return array_combine($headers, $row);
        }, $rows);

        $service = new UserImportService(
            new CountryFlyweightFactory(),
            new UserTypeFlyweightFactory()
        );

        $users = $service->importFromArray($data);

        $this->assertCount(count($data), $users);

        $first = $users[0];
        $second = $users[1];

        $this->assertSame(
            $first->getType(),
            $second->getType(),
            'UserType should be shared flyweight object if equal'
        );

        $this->assertSame(
            $first->getCountry(),
            $users[9]->getCountry(),
            'Country should also be shared flyweight if value matches'
        );

        $this->assertEquals('User1', $first->getName());
        $this->assertEquals('user1@user.com', $first->getEmail());
    }
}
