<?php

namespace SlmQueue\Command;

use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Exception\ExceptionInterface;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Worker\WorkerPluginManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Worker CLI command
 */
class StartWorkerCommand extends Command
{
    protected QueuePluginManager $queuePluginManager;
    protected WorkerPluginManager $workerPluginManager;

    public function __construct(QueuePluginManager $queuePluginManager, WorkerPluginManager $workerPluginManager)
    {
        parent::__construct();

        $this->queuePluginManager = $queuePluginManager;
        $this->workerPluginManager = $workerPluginManager;
    }

    protected function configure(): void
    {
        $this->addArgument('queue', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueName = $input->getArgument('queue');
        $queue = $this->queuePluginManager->get($queueName);
        $worker = $this->workerPluginManager->get($queue->getWorkerName());

        try {
            $messages = $worker->processQueue($queue, $input->getArguments());
        } catch (ExceptionInterface $e) {
            throw new WorkerProcessException(
                'Caught exception while processing queue',
                $e->getCode(),
                $e
            );
        }

        $messages = implode("\n", array_map(function (string $message): string {
            return sprintf(' - %s', $message);
        }, $messages));

        $output->writeln(sprintf(
            "Finished worker for queue '%s':\n%s\n",
            $queueName,
            $messages
        ));

        return 0;
    }
}
