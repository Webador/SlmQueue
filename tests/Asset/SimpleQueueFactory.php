<?php

namespace SlmQueueTest\Asset;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Worker\WorkerPluginManager;

class SimpleQueueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SimpleQueue
    {
        $jobPluginManager = new JobPluginManager($container);
        $workerPluginManager = new WorkerPluginManager($container);

        return new SimpleQueue($requestedName, $jobPluginManager, $workerPluginManager);
    }
}
