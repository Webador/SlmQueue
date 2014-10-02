<?php

namespace SlmQueue\Factory;

use SlmQueue\Strategy\StrategyPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * StrategyPluginManagerFactory
 */
class StrategyPluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['slm_queue']['strategy_manager'];

        $listenerPluginManager = new StrategyPluginManager(new Config($config));
        $listenerPluginManager->setServiceLocator($serviceLocator);

        return $listenerPluginManager;
    }
}
