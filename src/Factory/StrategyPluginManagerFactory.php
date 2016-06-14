<?php

namespace SlmQueue\Factory;

use SlmQueue\Strategy\StrategyPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * StrategyPluginManagerFactory
 */
class StrategyPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = $config['slm_queue']['strategy_manager'];
        $listenerPluginManager = new StrategyPluginManager($container, $config);

        return $listenerPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, StrategyPluginManager::class);
    }
}
