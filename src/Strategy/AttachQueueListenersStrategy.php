<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;

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
        $this->pluginManager = $pluginManager;
        $this->strategyConfig = $strategyConfig;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_BOOTSTRAP,
            [$this, 'attachQueueListeners'],
            PHP_INT_MAX
        );
    }

    public function attachQueueListeners(BootstrapEvent $bootstrapEvent): void
    {
        /** @var AbstractWorker $worker */
        $worker = $bootstrapEvent->getWorker();
        $name = $bootstrapEvent->getQueue()->getName();
        $eventManager = $worker->getEventManager();

        $this->detach($eventManager);

        if (! isset($this->strategyConfig[$name])) {
            $name = 'default'; // We want to make sure the default process queue is always attached
        }

        $strategies = $this->strategyConfig[$name];

        foreach ($strategies as $strategy => $options) {
            // no options given, name stored as value
            if (is_numeric($strategy) && is_string($options)) {
                $strategy = $options;
                $options = [];
            }

            if (! is_string($strategy) || ! is_array($options)) {
                continue;
            }

            $priority = null;
            if (isset($options['priority'])) {
                $priority = $options['priority'];
                unset($options['priority']);
            }

            /** @var ListenerAggregateInterface $listener */
            $listener = $this->pluginManager->get($strategy, $options);

            if ($priority !== null) {
                $listener->attach($eventManager, $priority);
            } else {
                $listener->attach($eventManager);
            }
        }

        $bootstrapEvent->stopPropagation();
        $eventManager->triggerEvent(new BootstrapEvent($worker, $bootstrapEvent->getQueue()));
    }
}
