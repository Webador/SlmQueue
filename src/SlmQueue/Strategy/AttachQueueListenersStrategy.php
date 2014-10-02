<?php

namespace SlmQueue\Strategy;

use SlmQueue\Exception\RuntimeException;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class AttachQueueListenersStrategy extends AbstractStrategy
{
    /**
     * @var StrategyPluginManager
     */
    protected $pluginManager;

    /**
     * @var array
     */
    protected $strategyConfig;

    /**
     * @param StrategyPluginManager $pluginManager
     * @param array                 $strategyConfig
     */
    public function __construct(StrategyPluginManager $pluginManager, array $strategyConfig)
    {
        $this->pluginManager  = $pluginManager;
        $this->strategyConfig = $strategyConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_BOOTSTRAP,
            array($this, 'attachQueueListeners'),
            PHP_INT_MAX
        );
    }

    /**
     * @param WorkerEvent $e
     * @throws \SlmQueue\Exception\RuntimeException
     */
    public function attachQueueListeners(WorkerEvent $e)
    {
        /** @var AbstractWorker $worker */
        $worker = $e->getTarget();
        $name = $e->getQueue()->getName();
        $eventManager = $worker->getEventManager();

        $eventManager->detachAggregate($this);

        if (!isset($this->strategyConfig[$name])) {
            return;
        }

        $strategies = $this->strategyConfig[$name];

        foreach ($strategies as $strategy => $options) {
            if (is_numeric($strategy) && is_string($options)) { // no options given, name stored as value
                $strategy = $options;
                $options = array();
            }

            if (!is_string($strategy) || !is_array($options)) {
                continue;
            }

            $priority = null;
            if (isset($options['priority'])) {
                $priority = $options['priority'];
                unset($options['priority']);
            }

            $listener = $this->pluginManager->get($strategy, $options);

            if (!is_null($priority)) {
                $eventManager->attachAggregate($listener, $priority);
            } else {
                $eventManager->attachAggregate($listener);
            }
        }

        if (!in_array(WorkerEvent::EVENT_PROCESS, $eventManager->getEvents())) {
            throw new RuntimeException(sprintf(
                "No worker strategy has been registered to respond to the '%s' event.",
                WorkerEvent::EVENT_PROCESS
            ));
        }

        $e->stopPropagation();
        $eventManager->trigger(WorkerEvent::EVENT_BOOTSTRAP, $e);
    }
}
