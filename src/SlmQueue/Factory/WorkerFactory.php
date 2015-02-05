<?php
namespace SlmQueue\Factory;

use SlmQueue\Exception\RuntimeException;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueue\Worker\WorkerEvent;
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

    /**
     * @param EventManagerInterface $eventManager
     * @param StrategyPluginManager $listenerPluginManager
     * @param array $strategyConfig
     * @throws RuntimeException
     */
    protected function attachWorkerListeners(
        EventManagerInterface $eventManager,
        StrategyPluginManager $listenerPluginManager,
        array $strategyConfig = array()
    ) {
        foreach ($strategyConfig as $strategy => $options) {
            // no options given, name stored as value
            if (is_numeric($strategy) && is_string($options)) {
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

            if (!is_null($priority)) {
                $eventManager->attachAggregate($listener, $priority);
            } else {
                $eventManager->attachAggregate($listener);
            }
        }

        if (!in_array(WorkerEvent::EVENT_BOOTSTRAP, $eventManager->getEvents())) {
            throw new RuntimeException(sprintf(
                "No worker strategy has been registered to respond to the '%s' event.",
                WorkerEvent::EVENT_BOOTSTRAP
            ));
        }
    }
}
