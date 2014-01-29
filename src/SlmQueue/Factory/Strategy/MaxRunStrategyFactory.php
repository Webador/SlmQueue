<?php

namespace SlmQueue\Factory\Strategy;

use SlmQueue\Listener\ListenerPluginManager;
use SlmQueue\Listener\Strategy\MaxRunsStrategy;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * MaxRunStrategyFactory
 */
class MaxRunStrategyFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // We do not need to check if jobs is an empty array because every the JobPluginManager automatically
        // adds invokables if the job name is not known, which will be sufficient most of the time
        $config = $serviceLocator->get('Config');
        $config = $config['slm_queue']['worker'];

        $strategy = new MaxRunsStrategy('');

        return $strategy;
    }
}
