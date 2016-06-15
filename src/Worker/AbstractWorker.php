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

        $workerEvent = new WorkerEvent($this, $queue);
        $workerEvent->setName(WorkerEvent::EVENT_BOOTSTRAP);
        $workerEvent->setOptions($options);
        $eventManager->triggerEvent($workerEvent);

        $shouldExitWorkerLoop = false;
        while (!$shouldExitWorkerLoop) {
            $workerEvent = new WorkerEvent($this, $queue);
            $workerEvent->setName(WorkerEvent::EVENT_PROCESS_QUEUE);
            $workerEvent->setOptions($options);

            $results = $eventManager->triggerEvent($workerEvent);
            foreach($results as $returnedWorkerEvent) {
                if ($returnedWorkerEvent instanceof WorkerEvent && $returnedWorkerEvent->shouldExitWorkerLoop()) {
                    $shouldExitWorkerLoop = true;                }
            }
        }

        $workerEvent = new WorkerEvent($this, $queue);
        $workerEvent->setName(WorkerEvent::EVENT_FINISH);
        $workerEvent->setOptions($options);
        $eventManager->triggerEvent($workerEvent);

        $workerEvent = new WorkerEvent($this, $queue);
        $workerEvent->setName(WorkerEvent::EVENT_PROCESS_STATE);
        $workerEvent->setOptions($options);
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
