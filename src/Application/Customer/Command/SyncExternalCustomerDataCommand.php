<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;

/**
 * Class SyncExternalCustomerDataCommand
 *
 * Command to synchronize external customer data with an external system (CRM, ERP, etc).
 * This command will interact with the CustomerSyncService to perform the synchronization.
 */
class SyncExternalCustomerDataCommand implements CommandInterface
{
    public function __construct(private CustomerService $customerService) {}

    public function label(): string
    {
        return 'Sync customer data';
    }

    public function execute(CustomerCommandPayload $payload): CustomerCommandResult
    {
        return new CustomerCommandResult($this->customerService->syncCustomerData());
    }
}
