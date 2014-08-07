<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Listener\Strategy\AbstractStrategy;
use SlmQueue\Listener\Strategy\LogJobStrategy;
use SlmQueue\Queue\QueueInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ResponseCollection;
use Zend\Stdlib\ArrayUtils;

/**
 * AbstractWorker
 */
abstract class AbstractWorker implements WorkerInterface, EventManagerAwareInterface
{

    /**
     * @var ListenerPluginManager
     */
    protected $listenerPluginManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    public function processQueue(QueueInterface $queue, array $options = array())
    {
        $eventManager = $this->getEventManager();
        $workerEvent  = new WorkerEvent($queue);

        if (array_key_exists('verbose', $options) && true === $options['verbose']) {
            $eventManager->attachAggregate(new LogJobStrategy());
        }

        // Initializer listener attached many strategies
        $eventManager->trigger(ListenerEvent::EVENT_PROCESS_PRE, new ListenerEvent($queue));

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_PRE, $workerEvent);

        while (!$workerEvent->exitWorkerLoop()) {
            $job = $queue->pop($options);

            // The queue may return null, for instance if a timeout was set
            if (!$job instanceof JobInterface) {
                $eventManager->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $workerEvent);

                continue;
            }

            $workerEvent->setJob($job);

            $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_PRE, $workerEvent);

            $result = $this->processJob($job, $queue);

            $workerEvent->setResult($result);

            $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_POST, $workerEvent);
        }

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_POST, $workerEvent);

        $queueState = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_REPORT, $workerEvent);

        // Initializer detaches strategies
        $eventManager->trigger(ListenerEvent::EVENT_PROCESS_POST, new ListenerEvent($queue));

        return $queueState;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $eventManager)
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
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }
}
