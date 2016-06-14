<?php

namespace SlmQueue\Strategy;

use SlmQueue\Exception\RuntimeException;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

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
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_BOOTSTRAP,
            [$this, 'attachQueueListeners'],
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
        $worker       = $e->getTarget();
        $name         = $e->getQueue()->getName();
        $eventManager = $worker->getEventManager();

        $this->detach($eventManager);

        if (!isset($this->strategyConfig[$name])) {
            $name = 'default'; // We want to make sure the default process queue is always attached
        }

        $strategies = $this->strategyConfig[$name];

        foreach ($strategies as $strategy => $options) {
            // no options given, name stored as value
            if (is_numeric($strategy) && is_string($options)) {
                $strategy = $options;
                $options = [];
            }

            if (!is_string($strategy) || !is_array($options)) {
                continue;
            }

            $priority = null;
            if (isset($options['priority'])) {
                $priority = $options['priority'];
                unset($options['priority']);
            }

            /** @var ListenerAggregateInterface $listener */
            $listener = $this->pluginManager->get($strategy, $options);

            if (!is_null($priority)) {
                $listener->attach($eventManager, $priority);
            } else {
                $listener->attach($eventManager);
            }
        }

        $e->stopPropagation();
        $e->setName(WorkerEvent::EVENT_BOOTSTRAP);
        $eventManager->triggerEvent($e);
    }
}
