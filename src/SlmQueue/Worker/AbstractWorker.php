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

        $exitRequested = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_PRE, $workerEvent)->stopped();

        while (!$exitRequested) {
            $job = $queue->pop($options);

            // The queue may return null, for instance if a timeout was set
            if (!$job instanceof JobInterface) {
                $exitRequested = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $workerEvent)->stopped();

                continue;
            }

            $workerEvent->setJob($job);
            $workerEvent->setResult(WorkerEvent::JOB_STATUS_UNKNOWN);

            // strategies may request an exit, however the job must be processed for this event
            $exitRequested = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_PRE, $workerEvent)->stopped();

            $result = $this->processJob($job, $queue);

            $workerEvent->setResult($result);

            $exitRequested = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_POST, $workerEvent)->stopped() || $exitRequested;
        }

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_POST, $workerEvent);

        // Initializer detaches strategies and collects exit states
        $exitStates = $eventManager->trigger(ListenerEvent::EVENT_PROCESS_POST, new ListenerEvent($queue))->first();

        return $exitStates;
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
