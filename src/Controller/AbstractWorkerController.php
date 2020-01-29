<?php

namespace SlmQueue\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Exception\ExceptionInterface;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Worker\WorkerInterface;

abstract class AbstractWorkerController extends AbstractActionController
{
    /**
     * @var WorkerInterface
     */
    protected $worker;

    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @param WorkerInterface    $worker
     * @param QueuePluginManager $queuePluginManager
     */
    public function __construct(WorkerInterface $worker, QueuePluginManager $queuePluginManager)
    {
        $this->worker = $worker;
        $this->queuePluginManager = $queuePluginManager;
    }

    public function processAction(): string
    {
        $options = $this->params()->fromRoute();
        $name = $options['queue'];
        $queue = $this->queuePluginManager->get($name);

        try {
            $messages = $this->worker->processQueue($queue, $options);
        } catch (ExceptionInterface $e) {
            throw new WorkerProcessException(
                'Caught exception while processing queue',
                $e->getCode(),
                $e
            );
        }

        return $this->formatOutput($name, $messages);
    }

    protected function formatOutput(string $queueName, array $messages = []): string
    {
        $messages = implode("\n", array_map(function (string $message): string {
            return sprintf(' - %s', $message);
        }, $messages));

        return sprintf(
            "Finished worker for queue '%s':\n%s\n",
            $queueName,
            $messages
        );
    }
}
