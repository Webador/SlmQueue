<?php

namespace SlmQueue\Factory;

use SlmQueue\Controller\Plugin\QueuePlugin;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * QueueControllerPluginFactory
 */
class QueueControllerPluginFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator     = $serviceLocator->getServiceLocator();
        $queuePluginManager = $serviceLocator->get('SlmQueue\Queue\QueuePluginManager');
        $jobPluginManager   = $serviceLocator->get('SlmQueue\Job\JobPluginManager');

        return new QueuePlugin($queuePluginManager, $jobPluginManager);
    }
}
