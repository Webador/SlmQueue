<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Listener\Strategy\AbstractStrategy;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Queue\QueuePluginManager;
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
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Constructor
     *
     * @param QueuePluginManager $queuePluginManager
     */
    public function __construct(QueuePluginManager $queuePluginManager)
    {
        $this->queuePluginManager    = $queuePluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue($queueName, array $options = array())
    {
        /** @var $queue QueueInterface */
        $queue        = $this->queuePluginManager->get($queueName);
        $eventManager = $this->getEventManager();
        $workerEvent  = new WorkerEvent($queue);

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

            // strategies may request an exit, however the job must be processed for this event
            $exitRequested = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_PRE, $workerEvent)->stopped();

            $this->processJob($job, $queue);

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
