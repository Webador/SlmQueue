<?php

namespace SlmQueue\Listener;

use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;

class WorkerInitializerListenerAggregate extends AbstractListenerAggregate
{
    /**
     * @var AbstractWorker
     */
    protected $worker;

    /**
     * @var StrategyPluginManager
     */
    protected $strategyPluginManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * Keeps reference to attached strategies for detaching
     * @var array
     */
    protected $strategies = array();

    public function __construct(AbstractWorker $worker, StrategyPluginManager $strategyPluginManager, array $options)
    {
        $this->worker                = $worker;
        $this->strategyPluginManager = $strategyPluginManager;
        $this->options               = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_PRE, array($this, 'onAttachQueueListeners'));
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_POST, array($this, 'onDetachQueueListeners'));
    }

    public function onAttachQueueListeners(WorkerEvent $event)
    {
        $queueName = $event->getQueue()->getName();

        if (!array_key_exists($queueName, $this->options['queues'])) {
            $this->options['queues'][$queueName] = array();
        }

        // normalize and merge strategy configuration with common and queue ones.
        $listenersOptions = array_merge( $this->options['common'], $this->options['queues'][$queueName]);
        $normalizedOptions = array();

        foreach ($listenersOptions as $listenerOptions) {
            $options  = null;
            $priority = null;

            if (is_string($listenerOptions)) {
                $name = $listenerOptions;
            } elseif (is_array($listenerOptions)) {
                $name     = $listenerOptions['name'];
                if (array_key_exists('options', $listenerOptions)) {
                    $options = $listenerOptions['options'];
                }
                if (array_key_exists('priority', $listenerOptions)) {
                    $priority = $listenerOptions['priority'];
                }
            }

            if (array_key_exists($name, $normalizedOptions)) {
                $normalizedOptions[$name] = ArrayUtils::merge(
                    $normalizedOptions[$name],
                    array_filter(array('name' => $name, 'options' => $options, 'priority' => $priority))
                );
            } else {
                $normalizedOptions[$name] = array_filter(array('name' => $name, 'options' => $options, 'priority' => $priority));
            }
        }

        foreach ($normalizedOptions as $name => $normalizedListenerOptions) {
            $listener = $this->strategyPluginManager->get($name);

            if (array_key_exists('options', $normalizedListenerOptions) && method_exists($listener, 'setOptions')) {
                $listener->setOptions($normalizedListenerOptions['options']);
            }

            if (array_key_exists('priority', $normalizedListenerOptions)) {
                $this->worker->getEventManager()->attachAggregate($listener, $normalizedListenerOptions['priority']);
            } else {
                $this->worker->getEventManager()->attachAggregate($listener);
            }

            $this->strategies[] = $listener;
        }
    }

    public function onDetachQueueListeners(WorkerEvent $event)
    {
        while(count($this->strategies)) {
            $this->worker->getEventManager()->detachAggregate(array_pop($this->strategies));
        }
    }

}
