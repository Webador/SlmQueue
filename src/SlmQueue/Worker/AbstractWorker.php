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

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_PRE, $workerEvent);

        /** @var ResponseCollection $results */
        $results  = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_PRE, $workerEvent);
        $messages = array();

        while (!$results->stopped()) {
            $job = $queue->pop($options);

            // The queue may return null, for instance if a timeout was set
            if (!$job instanceof JobInterface) {
                $results = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_IDLE, $workerEvent);

                continue;
            }

            $workerEvent->setJob($job);

            $results = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_PRE, $workerEvent);

            $this->processJob($job, $queue);

            $results = $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_POST, $workerEvent);
        }

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_POST, $workerEvent);

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_POST, $workerEvent);

        foreach($results as $key=>$message) {
            $messages[] = $message;
        }

        return $messages;
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
