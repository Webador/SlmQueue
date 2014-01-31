<?php
namespace SlmQueue\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * WorkerFactory
 */
class WorkerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        $workerOptions         = $serviceLocator->get('SlmQueue\Options\WorkerOptions');
        $listenerPluginManager = $serviceLocator->get('SlmQueue\Listener\ListenerPluginManager');
        $queuePluginManager    = $serviceLocator->get('SlmQueue\Queue\QueuePluginManager');

        return new $requestedName($queuePluginManager, $listenerPluginManager, $workerOptions);
    }
}
