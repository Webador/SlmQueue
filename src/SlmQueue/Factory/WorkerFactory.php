<?php
namespace SlmQueue\Factory;

use SlmQueue\Listener\WorkerInitializerListenerAggregate;
use SlmQueue\Worker\AbstractWorker;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * WorkerFactory
 */
class WorkerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param null $canonicalName
     * @param null $requestedName
     * @return AbstractWorker
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        $config                = $serviceLocator->get('Config');
        $strategies            = $config['slm_queue']['strategies'];

        $listenerPluginManager = $serviceLocator->get('SlmQueue\Listener\StrategyPluginManager');
        $queuePluginManager    = $serviceLocator->get('SlmQueue\Queue\QueuePluginManager');

        /** @var AbstractWorker $worker */
        $worker                = new $requestedName($queuePluginManager);
        $attachQueueListener   = new WorkerInitializerListenerAggregate($worker, $listenerPluginManager, $strategies);

        $worker->getEventManager()->attachAggregate($attachQueueListener);

        return $worker;
    }
}
