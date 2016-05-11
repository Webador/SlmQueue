<?php

namespace SlmQueue\Factory;

use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        $config = new Config($config);
        /*
         * For SM2 compatible
         */
        $config = method_exists($config, 'toArray')?$config->toArray():$config;
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
