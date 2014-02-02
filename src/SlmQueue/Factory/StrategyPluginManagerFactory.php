<?php

namespace SlmQueue\Factory;

use SlmQueue\Listener\StrategyPluginManager;
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
        // We do not need to check if jobs is an empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $serviceLocator->get('Config');
        $config = $config['slm_queue']['strategy_manager'];

        $listenerPluginManager = new StrategyPluginManager(new Config($config));
        $listenerPluginManager->setServiceLocator($serviceLocator);

        return $listenerPluginManager;
    }
}
