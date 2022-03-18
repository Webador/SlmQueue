<?php

namespace SlmQueueTest\Asset;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Job\JobPluginManager;

class SimpleQueueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SimpleQueue
    {
        $jobPluginManager = new JobPluginManager($container);

        return new SimpleQueue($requestedName, $jobPluginManager);
    }
}
