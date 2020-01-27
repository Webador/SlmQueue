<?php

namespace SlmQueue\Factory;

use SlmQueue\Queue\QueuePluginManager;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * QueuePluginManagerFactory
 */
class QueuePluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = $config['slm_queue']['queue_manager'];
        $queuePluginManager = new QueuePluginManager($container, $config);

        return $queuePluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, QueuePluginManager::class);
    }
}
