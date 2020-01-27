<?php

namespace SlmQueue\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use SlmQueue\Strategy\StrategyPluginManager;

class StrategyPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ): StrategyPluginManager {
        $config = $container->get('config');
        $config = $config['slm_queue']['strategy_manager'];

        return new StrategyPluginManager($container, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StrategyPluginManager
    {
        return $this($serviceLocator, StrategyPluginManager::class);
    }
}
