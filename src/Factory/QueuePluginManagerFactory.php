<?php

namespace SlmQueue\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use SlmQueue\Queue\QueuePluginManager;

class QueuePluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueuePluginManager
    {
        $config = $container->get('config');
        $config = $config['slm_queue']['queue_manager'];

        return new QueuePluginManager($container, $config);
    }
}
