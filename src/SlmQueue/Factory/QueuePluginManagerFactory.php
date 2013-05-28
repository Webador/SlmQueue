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
        $config = $serviceLocator->get('Config');
        $config = $config['slm_queue']['queue_manager'];

        $queuePluginManager = new QueuePluginManager(new Config($config));
        $queuePluginManager->setServiceLocator($serviceLocator);

        return $queuePluginManager;
    }
}
