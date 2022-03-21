<?php

namespace SlmQueueTest\Asset;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Job\JobPluginManager;

class FileQueueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileQueue
    {
        $config = $container->get('config')[FileQueue::class];
        $jobPluginManager = new JobPluginManager($container);

        return new FileQueue($config['filename'], $requestedName, $jobPluginManager);
    }
}
