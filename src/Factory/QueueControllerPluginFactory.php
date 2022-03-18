<?php

namespace SlmQueue\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;

class QueueControllerPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueuePlugin
    {
        $queuePluginManager = $container->get(QueuePluginManager::class);
        $jobPluginManager = $container->get(JobPluginManager::class);

        return new QueuePlugin($queuePluginManager, $jobPluginManager);
    }
}
