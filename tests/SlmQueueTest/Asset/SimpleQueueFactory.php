<?php

namespace SlmQueueTest\Asset;
use SlmQueue\Job\JobPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * SimpleQueueFactory
 */
class SimpleQueueFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = '', $requestedName = '')
    {
        $jobPluginManager = new JobPluginManager();

        $queue = new SimpleQueue($requestedName, $jobPluginManager);

        return $queue;
    }
}
