<?php

namespace SlmQueue\Worker;

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

    public function __construct(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
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

        while (!$workerEvent->shouldExitWorkerLoop()) {
            $eventManager->trigger(WorkerEvent::EVENT_EMIT, $workerEvent);
        }

        $eventManager->trigger(WorkerEvent::EVENT_FINISH, $workerEvent);

        $queueState = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_STATE, $workerEvent);

        $queueState = array_filter(ArrayUtils::iteratorToArray($queueState));

        return $queueState;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
