<?php

namespace App\Application\Customer\Command;

use App\Application\Customer\Service\CustomerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;

/**
 * Command to create a new customer.
 */
class CreateCustomerCommand extends Command
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        parent::__construct();
        $this->customerService = $customerService;
    }

    protected function configure(): void
    {
        $this->setName('customer:create')
            ->setDescription('Create a new customer');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Ask for customer name
        $nameQuestion = new Question('Please enter customer name: ');
        $name = $helper->ask($input, $output, $nameQuestion);

        // Ask for customer email
        $emailQuestion = new Question('Please enter customer email: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // Call the service to create the customer
        $this->customerService->createCustomer($name, $email);

        $output->writeln("<info>Customer {$name} created successfully.</info>");

        return Command::SUCCESS;
    }
}
