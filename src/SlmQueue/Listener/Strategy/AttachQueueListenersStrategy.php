<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Listener\StrategyPluginManager;
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

    public function attachQueueListeners(WorkerEvent $e)
    {
        /** @var AbstractWorker $worker */
        $worker = $e->getTarget();
        $name = $e->getQueue()->getName();
        $eventManager = $worker->getEventManager();

        $eventManager->detachAggregate($this);

        if (!array_key_exists($name, $this->strategyConfig)) {
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

        $e->stopPropagation();
        $eventManager->trigger(WorkerEvent::EVENT_BOOTSTRAP, $e);
    }
} 