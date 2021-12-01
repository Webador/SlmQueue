<?php

namespace SlmQueueTest\Asset;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Worker\WorkerPluginManager;

class FileQueueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileQueue
    {
        $config = $container->get('config')[FileQueue::class];
        $jobPluginManager = new JobPluginManager($container);
        $workerPluginManager = new WorkerPluginManager($container);

        return new FileQueue($config['filename'], $requestedName, $jobPluginManager, $workerPluginManager);
    }
}
