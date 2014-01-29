<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;
use SlmQueue\Listener\ListenerPluginManager;
use SlmQueue\Listener\ListenerInterface;
use SlmQueue\Listener\Strategy\AbstractStrategy;
use SlmQueue\Options\WorkerOptions;
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
     * @var ListenerPluginManager
     */
    protected $listenerPluginManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var WorkerOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param QueuePluginManager $queuePluginManager
     * @param WorkerOptions      $options
     */
    public function __construct(QueuePluginManager $queuePluginManager, ListenerPluginManager $listenerPluginManager, WorkerOptions $options)
    {
        $this->queuePluginManager    = $queuePluginManager;
        $this->listenerPluginManager = $listenerPluginManager;
        $this->options               = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue($queueName, array $options = array())
    {
        /** @var $queue QueueInterface */
        $queue        = $this->queuePluginManager->get($queueName);
        $eventManager = $this->getEventManager();
        $this->configureStrategies();

        $workerEvent = new WorkerEvent($queue);

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

        foreach($results as $key=>$message) {
            $messages[] = $message;
        }

        return $messages;
    }

    protected function configureStrategies(array $strategies = array())
    {
        $strategies_required = array(
            array('name'=>'SlmQueue\Strategy\InterruptStrategy'),
            array('name'=>'SlmQueue\Strategy\MaxMemoryStrategy', 'options' => $this->options->toArray()),
            array('name'=>'SlmQueue\Strategy\MaxRunsStrategy', 'options' => $this->options->toArray())
        );

        $strategies = ArrayUtils::merge($strategies, $strategies_required);
try {
    $this->addStrategies($strategies);

} catch (\Exception $e) {
    print_r($e->getMessage());
}
    }

    protected function addStrategies(array $strategies)
    {
        foreach ($strategies as $strategy) {
            if (is_string($strategy)) {
                $listener = $this->listenerPluginManager->get($strategy);
                $this->addStrategy($listener);
            } elseif (is_array($strategy)) {
                $name     = $strategy['name'];
                $listener = $this->listenerPluginManager->get($name);
                if (array_key_exists('options', $strategy) && method_exists($listener, 'setOptions')) {
                    $listener->setOptions($strategy['options']);
                }

                $priority = 1;
                if (array_key_exists('priority', $strategy)) {
                    $priority = $strategy['priority'];
                }

                $this->addStrategy($listener, $priority);
            }
        }
    }


    public function addStrategy(AbstractStrategy $strategy, $priority = 1)
    {
        $this->getEventManager()->attachAggregate($strategy, $priority);
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
