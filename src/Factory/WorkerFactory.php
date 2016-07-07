<?php
namespace SlmQueue\Factory;

use Interop\Container\ContainerInterface;
use SlmQueue\Exception\RuntimeException;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueue\Worker\WorkerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * WorkerFactory
 */
class WorkerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config     = $container->get('config');
        $strategies = $config['slm_queue']['worker_strategies']['default'];

        $eventManager          = $container->get('EventManager');
        $listenerPluginManager = $container->get(StrategyPluginManager::class);
        $this->attachWorkerListeners($eventManager, $listenerPluginManager, $strategies);

        /** @var WorkerInterface $worker */
        $worker = new $requestedName($eventManager);

        return $worker;
    }

    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  null                    $canonicalName
     * @param  null                    $requestedName
     * @return WorkerInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * @param EventManagerInterface $eventManager
     * @param StrategyPluginManager $listenerPluginManager
     * @param array                 $strategyConfig
     * @throws RuntimeException
     */
    protected function attachWorkerListeners(
        EventManagerInterface $eventManager,
        StrategyPluginManager $listenerPluginManager,
        array $strategyConfig = []
    ) {
        foreach ($strategyConfig as $strategy => $options) {
            // no options given, name stored as value
            if (is_numeric($strategy) && is_string($options)) {
                $strategy = $options;
                $options  = [];
            }

            if (!is_string($strategy) || !is_array($options)) {
                continue;
            }

            $priority = null;
            if (isset($options['priority'])) {
                $priority = $options['priority'];
                unset($options['priority']);
            }

            $listener = $listenerPluginManager->get($strategy, $options);

            if (!is_null($priority)) {
                $listener->attach($eventManager, $priority);
            } else {
                $listener->attach($eventManager);
            }
        }
    }
}
