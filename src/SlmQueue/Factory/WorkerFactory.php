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

        $eventManager          = $serviceLocator->get('EventManager');
        $listenerPluginManager = $serviceLocator->get('SlmQueue\Listener\StrategyPluginManager');

        /** @var AbstractWorker $worker */
        $worker                = new $requestedName($eventManager);
        $attachQueueListener   = new WorkerInitializerListenerAggregate($worker, $listenerPluginManager, $strategies);

        $eventManager->attachAggregate($attachQueueListener);

        return $worker;
    }
}
