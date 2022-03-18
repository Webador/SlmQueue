<?php

namespace SlmQueue\Factory;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueue\Worker\WorkerInterface;

class WorkerAbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return in_array(WorkerInterface::class, class_implements($requestedName), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WorkerInterface
    {
        $config = $container->get('config');
        $strategies = $config['slm_queue']['worker_strategies']['default'];

        $eventManager = $container->has('EventManager') ? $container->get('EventManager') : new EventManager();
        $listenerPluginManager = $container->get(StrategyPluginManager::class);
        $this->attachWorkerListeners($eventManager, $listenerPluginManager, $strategies);

        /** @var WorkerInterface $worker */
        $worker = new $requestedName($eventManager);

        return $worker;
    }

    protected function attachWorkerListeners(
        EventManagerInterface $eventManager,
        StrategyPluginManager $listenerPluginManager,
        array $strategyConfig = []
    ): void {
        foreach ($strategyConfig as $strategy => $options) {
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

            $listener = $listenerPluginManager->get($strategy, $options);

            if ($priority !== null) {
                $listener->attach($eventManager, $priority);
            } else {
                $listener->attach($eventManager);
            }
        }
    }
}
