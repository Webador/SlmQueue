<?php

namespace SlmQueue\Factory;

use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * QueuePluginManagerFactory
 */
class QueuePluginManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['slm_queue']['queues'];
        if (empty($config)) {
            throw new Exception\RuntimeException('No queues were found in SlmQueue config');
        }

        return new QueuePluginManager(new Config($config));
    }
}
