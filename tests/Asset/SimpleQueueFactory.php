<?php

namespace SlmQueueTest\Asset;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use SlmQueue\Job\JobPluginManager;

/**
 * SimpleQueueFactory
 */
class SimpleQueueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SimpleQueue
    {
        $jobPluginManager = new JobPluginManager($container);

        return new SimpleQueue($requestedName, $jobPluginManager);
    }

    public function createService(ServiceLocatorInterface $serviceLocator, $name = '', $requestedName = ''): SimpleQueue
    {
        return $this($serviceLocator, $requestedName);
    }
}
