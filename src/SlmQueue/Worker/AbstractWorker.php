<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * AbstractWorker
 */
abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected $defaultListeners;

    public function __construct(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            get_called_class(),
            'SlmQueue\Worker\WorkerInterface'
        ));

        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue(QueueInterface $queue, array $options = array())
    {
        $eventManager = $this->eventManager;
        $workerEvent  = new WorkerEvent($this, $queue);

        $workerEvent->setOptions($options);

        $eventManager->trigger(WorkerEvent::EVENT_BOOTSTRAP, $workerEvent);

        while (!$workerEvent->shouldWorkerExitLoop()) {
            $eventManager->trigger(WorkerEvent::EVENT_PROCESS, $workerEvent);
        }

        $eventManager->trigger(WorkerEvent::EVENT_FINISH, $workerEvent);

        $queueState = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_STATE, $workerEvent);

        $queueState = array_filter(ArrayUtils::iteratorToArray($queueState));

        return $queueState;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }
}
