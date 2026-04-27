<?php

namespace App\Command;

use App\Application\Customer\Command\SyncExternalCustomerDataCommand;
use App\Application\Customer\Command\CreateCustomerCommand;
use App\Application\Customer\Command\UpdateCustomerCommand;
use App\Application\Customer\Command\DeleteCustomerCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * This class implements the Command Design Pattern, which encapsulates a request as an object.
 * It decouples the command invocation from the command execution, promoting flexibility,
 * scalability, and adherence to the single responsibility and open/closed principles.
 *
 * This pattern is especially useful in CLI applications where user input dynamically
 * triggers different operations, enabling clear separation between concerns and
 * a structured approach to handling executable tasks.
 *
 * Execute this example with ```php bin/console customer:interact```
 */
class CustomerInteractionCommand extends Command
{
    private SyncExternalCustomerDataCommand $syncExternalCustomerDataCommand;
    private CreateCustomerCommand $createCustomerCommand;
    private UpdateCustomerCommand $updateCustomerCommand;
    private DeleteCustomerCommand $deleteCustomerCommand;

    public function __construct(
        SyncExternalCustomerDataCommand $syncExternalCustomerDataCommand,
        CreateCustomerCommand $createCustomerCommand,
        UpdateCustomerCommand $updateCustomerCommand,
        DeleteCustomerCommand $deleteCustomerCommand
    ) {
        parent::__construct();
        $this->syncExternalCustomerDataCommand = $syncExternalCustomerDataCommand;
        $this->createCustomerCommand = $createCustomerCommand;
        $this->updateCustomerCommand = $updateCustomerCommand;
        $this->deleteCustomerCommand = $deleteCustomerCommand;
    }

    protected function configure(): void
    {
        $this->setName('customer:interact')
            ->setDescription('Interactively manage customer operations via the console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Create a question with options
        $question = new ChoiceQuestion(
            'Please select an action: ',
            [
                'Sync customer data',
                'Create a new customer',
                'Update customer details',
                'Delete customer',
            ],
            0 // Default choice (index starts from 0)
        );
        $question->setErrorMessage('Choice %s is invalid.');

        // Ask the user for input
        $selectedOption = $helper->ask($input, $output, $question);

        // Handle the user's choice and delegate to the corresponding command
        switch ($selectedOption) {
            case 'Sync customer data':
                $this->syncExternalCustomerDataCommand->execute();
                $output->writeln('<info>Customer data synchronized successfully.</info>');
                break;
            case 'Create a new customer':
                $this->createCustomerCommand->execute($input, $output);
                break;
            case 'Update customer details':
                $this->updateCustomerCommand->execute($input, $output);
                break;
            case 'Delete customer':
                $this->deleteCustomerCommand->execute($input, $output);
                break;
            default:
                $output->writeln('<error>Invalid option.</error>');
                break;
        }

        return Command::SUCCESS;
    }
}
