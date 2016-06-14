<?php

namespace SlmQueue\Factory;

use SlmQueue\Controller\Plugin\QueuePlugin;
use SlmQueue\Job\JobPluginManager;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        return $this($serviceLocator, QueuePlugin::class);
    }
}
