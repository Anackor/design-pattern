<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Command to update an existing customer's details.
 */
class UpdateCustomerCommand extends Command
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        parent::__construct();
        $this->customerService = $customerService;
    }

    protected function configure(): void
    {
        $this->setName('customer:update')
            ->setDescription('Update an existing customer details');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Ask for customer ID
        $idQuestion = new Question('Please enter customer ID: ');
        $customerId = $helper->ask($input, $output, $idQuestion);

        // Ask for the new customer name
        $nameQuestion = new Question('Please enter new customer name: ');
        $newName = $helper->ask($input, $output, $nameQuestion);

        // Ask for the new customer email
        $emailQuestion = new Question('Please enter new customer email: ');
        $newEmail = $helper->ask($input, $output, $emailQuestion);

        // Call the service to update the customer
        $this->customerService->updateCustomer($customerId, $newName, $newEmail);

        $output->writeln("<info>Customer {$customerId} updated successfully.</info>");

        return Command::SUCCESS;
    }
}
