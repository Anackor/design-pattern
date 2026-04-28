<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;

/**
 * Command object that encapsulates the request to delete a customer.
 */
class DeleteCustomerCommand implements CommandInterface
{
    public function __construct(private CustomerService $customerService) {}

    public function label(): string
    {
        return 'Delete customer';
    }

    public function execute(CustomerCommandPayload $payload): CustomerCommandResult
    {
        return new CustomerCommandResult(
            $this->customerService->deleteCustomer($payload->customerId())
        );
    }
}
