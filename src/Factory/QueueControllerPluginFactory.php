<?php

namespace SlmQueue\Factory;

use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * QueueControllerPluginFactory
 */
class QueueControllerPluginFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $queuePluginManager = $container->get(QueuePluginManager::class);
        $jobPluginManager   = $container->get(JobPluginManager::class);

        return new QueuePlugin($queuePluginManager, $jobPluginManager);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), QueuePlugin::class);
    }
}
