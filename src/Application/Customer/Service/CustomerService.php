<?php

namespace App\Application\Customer\Service;

/**
 * Service to handle customer-related operations.
 */
class CustomerService
{
    /**
     * Synchronize customer data with an external system (e.g., CRM or ERP).
     *
     * This function could involve making API calls, processing data, and handling errors.
     */
    public function syncCustomerData(): string
    {
        return 'Customer data synchronized with external system.';
    }

    public function createCustomer(string $name, string $email): string
    {
        return sprintf('Customer %s <%s> created.', $name, $email);
    }

    public function updateCustomer(int $customerId, string $newName, string $newEmail): string
    {
        return sprintf('Customer %d updated to %s <%s>.', $customerId, $newName, $newEmail);
    }

    public function deleteCustomer(int $customerId): string
    {
        return sprintf('Customer %d deleted.', $customerId);
    }
}
