<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Command to delete an existing customer.
 */
class DeleteCustomerCommand extends Command
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        parent::__construct();
        $this->customerService = $customerService;
    }

    protected function configure(): void
    {
        $this->setName('customer:delete')
            ->setDescription('Delete an existing customer');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Ask for customer ID
        $idQuestion = new Question('Please enter customer ID to delete: ');
        $customerId = $helper->ask($input, $output, $idQuestion);

        // Call the service to delete the customer
        $this->customerService->deleteCustomer($customerId);

        $output->writeln("<info>Customer {$customerId} deleted successfully.</info>");

        return Command::SUCCESS;
    }
}
