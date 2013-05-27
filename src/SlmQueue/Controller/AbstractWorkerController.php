<?php

namespace SlmQueue\Controller;

use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Exception\SlmQueueExceptionInterface;
use SlmQueue\Worker\WorkerInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * AbstractController
 */
abstract class AbstractWorkerController extends AbstractActionController
{
    /**
     * Get instance of worker
     *
     * @return WorkerInterface
     */
    abstract protected function getWorker();

    /**
     * Get options for worker
     *
     * @return array
     */
    abstract protected function getOptions();

    /**
     * Get name of queue
     *
     * @return string
     */
    abstract protected function getQueueName();

    /**
     * Process a queue
     *
     * @return string
     * @throws WorkerProcessingException
     */
    public function processAction()
    {
        $worker  = $this->getWorker();
        $options = $this->getOptions();
        $queue   = $this->getQueueName();

        try {
            $result = $worker->processQueue($queue, $options);
        } catch (SlmQueueExceptionInterface $e) {
            throw new WorkerProcessException(
                'Caught exception while processing queue',
                $e->getCode(), $e
            );
        }

        return sprintf(
            "Finished worker for queue %s with %s jobs",
            $queue,
            $result
        );
    }
}
