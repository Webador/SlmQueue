<?php

namespace SlmQueue\Controller;

use SlmQueue\Controller\Exception\WorkerProcessException;
use SlmQueue\Exception\ExceptionInterface;
use SlmQueue\Worker\WorkerInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * AbstractController
 */
abstract class AbstractWorkerController extends AbstractActionController
{
    /**
     * @var WorkerInterface
     */
    protected $worker;

    /**
     * @param WorkerInterface $worker
     */
    public function __construct(WorkerInterface $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Process a queue
     *
     * @return string
     * @throws WorkerProcessException
     */
    public function processAction()
    {
        $options = $this->params()->fromRoute();
        $queue   = $options['queue'];

        try {
            $result = $this->worker->processQueue($queue, $options);
        } catch (ExceptionInterface $e) {
            throw new WorkerProcessException(
                'Caught exception while processing queue',
                $e->getCode(), $e
            );
        }

        return sprintf(
            "Finished worker for queue '%s' with %s jobs\n",
            $queue,
            $result
        );
    }
}
