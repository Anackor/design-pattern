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
    private CustomerService $customerService;

    /**
     * Constructor for SyncExternalCustomerDataCommand.
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Execute the synchronization of external customer data.
     * It will use the CustomerSyncService to perform the actual data sync.
     */
    public function execute(): void
    {
        // Call the service to synchronize data
        $this->customerService->syncCustomerData();
    }
}
