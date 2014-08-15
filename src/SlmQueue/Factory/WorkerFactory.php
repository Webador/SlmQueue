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
        $strategies            = $config['slm_queue']['worker_strategies']['default'];

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
        foreach ($strategyConfig as $strategy => $options) {
            if (is_numeric($strategy) && is_string($options)) { // no options given, name stored as value
                $strategy = $options;
                $options = array();
            }

            if (!is_string($strategy) || !is_array($options)) {
                continue;
            }

            $priority = null;
            if (isset($options['priority'])) {
                $priority = $options['priority'];
                unset($options['priority']);
            }

            $listener = $listenerPluginManager->get($strategy, $options);

            $eventManager->attachAggregate($listener, $priority);
        }
    }
}
