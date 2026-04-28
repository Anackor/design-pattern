<?php

namespace App\Tests\Unit\Command;

use App\Application\Customer\Command\CreateCustomerCommand;
use App\Application\Customer\Command\DeleteCustomerCommand;
use App\Application\Customer\Command\SyncExternalCustomerDataCommand;
use App\Application\Customer\Command\UpdateCustomerCommand;
use App\Application\Customer\Service\CustomerService;
use App\Command\CustomerInteractionCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CustomerInteractionCommandTest extends TestCase
{
    private function createCommand(): CustomerInteractionCommand
    {
        $service = new CustomerService();

        $command = new CustomerInteractionCommand(
            new SyncExternalCustomerDataCommand($service),
            new CreateCustomerCommand($service),
            new UpdateCustomerCommand($service),
            new DeleteCustomerCommand($service)
        );

        $command->setHelperSet(new HelperSet([
            new QuestionHelper(),
        ]));

        return $command;
    }

    public function testSyncOptionExecutesSyncCommand(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->setInputs(['0']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Customer data synchronized with external system.', $tester->getDisplay());
    }

    public function testCreateOptionExecutesCreateCommand(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->setInputs(['1', 'Jane Doe', 'jane@example.com']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Customer Jane Doe <jane@example.com> created.', $tester->getDisplay());
    }

    public function testUpdateOptionExecutesUpdateCommand(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->setInputs(['2', '10', 'Jane Updated', 'jane.updated@example.com']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString(
            'Customer 10 updated to Jane Updated <jane.updated@example.com>.',
            $tester->getDisplay()
        );
    }

    public function testDeleteOptionExecutesDeleteCommand(): void
    {
        $tester = new CommandTester($this->createCommand());
        $tester->setInputs(['3', '10']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Customer 10 deleted.', $tester->getDisplay());
    }
}
