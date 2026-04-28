<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;

/**
 * Command object that encapsulates the request to update a customer.
 */
class UpdateCustomerCommand implements CommandInterface
{
    public function __construct(private CustomerService $customerService) {}

    public function label(): string
    {
        return 'Update customer details';
    }

    public function execute(CustomerCommandPayload $payload): CustomerCommandResult
    {
        return new CustomerCommandResult(
            $this->customerService->updateCustomer(
                $payload->customerId(),
                $payload->name(),
                $payload->email()
            )
        );
    }
}
