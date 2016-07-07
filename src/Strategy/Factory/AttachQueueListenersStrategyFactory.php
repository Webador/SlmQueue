<?php

namespace SlmQueue\Strategy\Factory;

use SlmQueue\Strategy\AttachQueueListenersStrategy;
use SlmQueue\Strategy\StrategyPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * AttachQueueListenersStrategyFactory
 */
class AttachQueueListenersStrategyFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pluginManager  = $container->get(StrategyPluginManager::class);
        $config         = $container->get('config');
        $strategyConfig = $config['slm_queue']['worker_strategies']['queues'];

        return new AttachQueueListenersStrategy($pluginManager, $strategyConfig);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AttachQueueListenersStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), AttachQueueListenersStrategy::class);
    }
}
