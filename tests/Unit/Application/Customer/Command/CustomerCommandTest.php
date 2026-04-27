<?php

namespace App\Tests\Unit\Application\Customer\Command;

use App\Application\Customer\Command\CreateCustomerCommand;
use App\Application\Customer\Command\CustomerCommandPayload;
use App\Application\Customer\Command\DeleteCustomerCommand;
use App\Application\Customer\Command\SyncExternalCustomerDataCommand;
use App\Application\Customer\Command\UpdateCustomerCommand;
use App\Application\Customer\Service\CustomerService;
use PHPUnit\Framework\TestCase;

class CustomerCommandTest extends TestCase
{
    public function testCreateCustomerCommandEncapsulatesRequest(): void
    {
        $command = new CreateCustomerCommand(new CustomerService());

        $result = $command->execute(CustomerCommandPayload::create('Jane Doe', 'jane@example.com'));

        $this->assertSame('Create a new customer', $command->label());
        $this->assertSame('Customer Jane Doe <jane@example.com> created.', $result->message());
    }

    public function testUpdateCustomerCommandEncapsulatesRequest(): void
    {
        $command = new UpdateCustomerCommand(new CustomerService());

        $result = $command->execute(CustomerCommandPayload::update(10, 'Jane Updated', 'jane.updated@example.com'));

        $this->assertSame('Customer 10 updated to Jane Updated <jane.updated@example.com>.', $result->message());
    }

    public function testDeleteCustomerCommandEncapsulatesRequest(): void
    {
        $command = new DeleteCustomerCommand(new CustomerService());

        $result = $command->execute(CustomerCommandPayload::delete(10));

        $this->assertSame('Customer 10 deleted.', $result->message());
    }

    public function testSyncCustomerCommandDoesNotNeedPayloadData(): void
    {
        $command = new SyncExternalCustomerDataCommand(new CustomerService());

        $result = $command->execute(CustomerCommandPayload::empty());

        $this->assertSame('Sync customer data', $command->label());
        $this->assertSame('Customer data synchronized with external system.', $result->message());
    }

    public function testPayloadRejectsMissingNameForCreateCommand(): void
    {
        $command = new CreateCustomerCommand(new CustomerService());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Customer command requires a customer name.');

        $command->execute(CustomerCommandPayload::empty());
    }
}
