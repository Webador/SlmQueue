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

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers([
            __CLASS__,
            get_called_class(),
            'SlmQueue\Worker\WorkerInterface'
        ]);

        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue(QueueInterface $queue, array $options = [])
    {
        $eventManager = $this->eventManager;
        $workerEvent  = new WorkerEvent($this, $queue);

        $workerEvent->setOptions($options);

        $workerEvent->setName(WorkerEvent::EVENT_BOOTSTRAP);
        $eventManager->triggerEvent($workerEvent);

        while (!$workerEvent->shouldExitWorkerLoop()) {
            $workerEvent->setName(WorkerEvent::EVENT_PROCESS_QUEUE);
            $results = $eventManager->triggerEvent($workerEvent);
            if ($results->stopped()) {
                $workerEvent = $results->last();
            };
        }

        $workerEvent->setName(WorkerEvent::EVENT_FINISH);
        $eventManager->triggerEvent($workerEvent);

        $workerEvent->setName(WorkerEvent::EVENT_PROCESS_STATE);
        $queueState = $eventManager->triggerEvent($workerEvent);

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
