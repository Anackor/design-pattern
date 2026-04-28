<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;

/**
 * Command object that encapsulates the request to create a customer.
 */
class CreateCustomerCommand implements CommandInterface
{
    public function __construct(private CustomerService $customerService) {}

    public function label(): string
    {
        return 'Create a new customer';
    }

    public function execute(CustomerCommandPayload $payload): CustomerCommandResult
    {
        return new CustomerCommandResult(
            $this->customerService->createCustomer($payload->name(), $payload->email())
        );
    }
}
