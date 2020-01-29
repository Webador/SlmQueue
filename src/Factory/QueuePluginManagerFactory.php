<?php

namespace SlmQueue\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use SlmQueue\Queue\QueuePluginManager;

class QueuePluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueuePluginManager
    {
        $config = $container->get('config');
        $config = $config['slm_queue']['queue_manager'];

        return new QueuePluginManager($container, $config);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): QueuePluginManager
    {
        return $this($serviceLocator, QueuePluginManager::class);
    }
}
