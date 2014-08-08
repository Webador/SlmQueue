<?php
namespace SlmQueue\Factory;

use SlmQueue\Listener\StrategyPluginManager;
use SlmQueue\Worker\WorkerInterface;
use Zend\EventManager\EventManagerInterface;
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
     * @param  ServiceLocatorInterface $serviceLocator
     * @param  null $canonicalName
     * @param  null $requestedName
     * @return WorkerInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        $config                = $serviceLocator->get('Config');
        $strategies            = $config['slm_queue']['strategies']['default'];

        $eventManager          = $serviceLocator->get('EventManager');
        $listenerPluginManager = $serviceLocator->get('SlmQueue\Listener\StrategyPluginManager');
        $this->attachWorkerListeners($eventManager, $listenerPluginManager, $strategies);

        /** @var WorkerInterface $worker */
        $worker = new $requestedName($eventManager);
        return $worker;
    }

    protected function attachWorkerListeners(
        EventManagerInterface $eventManager,
        StrategyPluginManager $listenerPluginManager,
        array $strategyConfig = array()
    ) {
        foreach ($strategyConfig as $strategy) {
            $options  = array();
            if (array_key_exists('options', $strategy)) {
                $options = $strategy['options'];
            }
            $priority = null;

            $listener = $listenerPluginManager->get($strategy['name'], $options);
            if (array_key_exists('priority', $strategy)) {
                $priority = $strategy['priority'];
                $eventManager->attachAggregate($listener, $priority);
            } else {
                $eventManager->attachAggregate($listener);
            }
        }
    }
}
