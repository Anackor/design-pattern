<?php

namespace App\Command;

use App\Application\Customer\Command\SyncExternalCustomerDataCommand;
use App\Application\Customer\Command\CreateCustomerCommand;
use App\Application\Customer\Command\CustomerCommandPayload;
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
    public function __construct(
        private SyncExternalCustomerDataCommand $syncExternalCustomerDataCommand,
        private CreateCustomerCommand $createCustomerCommand,
        private UpdateCustomerCommand $updateCustomerCommand,
        private DeleteCustomerCommand $deleteCustomerCommand
    ) {
        parent::__construct();
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
                $this->syncExternalCustomerDataCommand->label(),
                $this->createCustomerCommand->label(),
                $this->updateCustomerCommand->label(),
                $this->deleteCustomerCommand->label(),
            ],
            0 // Default choice (index starts from 0)
        );
        $question->setErrorMessage('Choice %s is invalid.');

        // Ask the user for input
        $selectedOption = $helper->ask($input, $output, $question);

        switch ($selectedOption) {
            case $this->syncExternalCustomerDataCommand->label():
                $result = $this->syncExternalCustomerDataCommand->execute(CustomerCommandPayload::empty());
                $output->writeln(sprintf('<info>%s</info>', $result->message()));
                break;
            case $this->createCustomerCommand->label():
                $result = $this->createCustomerCommand->execute($this->askCreatePayload($input, $output));
                $output->writeln(sprintf('<info>%s</info>', $result->message()));
                break;
            case $this->updateCustomerCommand->label():
                $result = $this->updateCustomerCommand->execute($this->askUpdatePayload($input, $output));
                $output->writeln(sprintf('<info>%s</info>', $result->message()));
                break;
            case $this->deleteCustomerCommand->label():
                $result = $this->deleteCustomerCommand->execute($this->askDeletePayload($input, $output));
                $output->writeln(sprintf('<info>%s</info>', $result->message()));
                break;
            default:
                $output->writeln('<error>Invalid option.</error>');
                break;
        }

        return Command::SUCCESS;
    }

    private function askCreatePayload(InputInterface $input, OutputInterface $output): CustomerCommandPayload
    {
        return CustomerCommandPayload::create(
            $this->ask($input, $output, 'Please enter customer name: '),
            $this->ask($input, $output, 'Please enter customer email: ')
        );
    }

    private function askUpdatePayload(InputInterface $input, OutputInterface $output): CustomerCommandPayload
    {
        return CustomerCommandPayload::update(
            (int) $this->ask($input, $output, 'Please enter customer ID: '),
            $this->ask($input, $output, 'Please enter new customer name: '),
            $this->ask($input, $output, 'Please enter new customer email: ')
        );
    }

    private function askDeletePayload(InputInterface $input, OutputInterface $output): CustomerCommandPayload
    {
        return CustomerCommandPayload::delete(
            (int) $this->ask($input, $output, 'Please enter customer ID to delete: ')
        );
    }

    private function ask(InputInterface $input, OutputInterface $output, string $question): string
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        return (string) $helper->ask($input, $output, new \Symfony\Component\Console\Question\Question($question));
    }
}
