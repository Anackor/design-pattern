<?php

namespace App\Command;

use App\Application\DTO\NotificationRequestDTO;
use App\Application\Notification\SendNotificationHandler;
use App\Application\UserActivity\UserActionDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ObservabilityDemoCommand provides a reproducible way to generate real structured logs.
 *
 * The intent is educational:
 * - trigger an Observer-based user activity event;
 * - trigger an outbound notification use case;
 * - print the exact JSON lines produced during this execution.
 *
 * This keeps observability tangible. Instead of telling readers that the project
 * "has logs", we give them a short command they can run and inspect immediately.
 */
class ObservabilityDemoCommand extends Command
{
    public function __construct(
        private UserActionDispatcher $userActionDispatcher,
        private SendNotificationHandler $sendNotificationHandler,
        private string $logFilePath
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:observability:demo')
            ->setDescription('Generate a small, reproducible structured logging demo')
            ->addOption(
                'reset-log',
                null,
                InputOption::VALUE_NONE,
                'Clear the observability log before generating the demo events'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ensureLogDirectoryExists();

        if ($input->getOption('reset-log')) {
            // Resetting the file makes the demo deterministic and easier to read during training.
            file_put_contents($this->logFilePath, '');
        }

        $initialSize = is_file($this->logFilePath) ? filesize($this->logFilePath) : 0;

        $output->writeln('<info>Generating structured observability demo...</info>');

        $this->userActionDispatcher->recordAction('demo-user-42', 'login');
        $this->userActionDispatcher->recordAction('demo-user-42', 'view_dashboard');

        $this->sendNotificationHandler->handle(new NotificationRequestDTO(
            'Observability demo',
            'This payload exists only to demonstrate structured logging.',
            'demo@example.com',
            'email'
        ));

        $output->writeln(sprintf('<comment>Log file:</comment> %s', $this->logFilePath));
        $output->writeln('<comment>Records generated in this execution:</comment>');
        $output->writeln($this->readNewRecords($initialSize));

        return Command::SUCCESS;
    }

    private function ensureLogDirectoryExists(): void
    {
        $directory = dirname($this->logFilePath);
        if (is_dir($directory)) {
            return;
        }

        mkdir($directory, 0o777, true);
    }

    private function readNewRecords(int $initialSize): string
    {
        $contents = (string) file_get_contents($this->logFilePath);

        if (0 === $initialSize) {
            return trim($contents);
        }

        return trim(substr($contents, $initialSize));
    }
}
