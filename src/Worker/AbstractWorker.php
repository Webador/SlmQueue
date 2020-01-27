<?php

namespace SlmQueue\Worker;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\FinishEvent;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\ProcessStateEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

/**
 * AbstractWorker
 */
abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers([
            __CLASS__,
            get_called_class(),
            'SlmQueue\Worker\WorkerInterface',
        ]);

        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue(QueueInterface $queue, array $options = [])
    {
        $this->eventManager->triggerEvent(new BootstrapEvent($this, $queue));

        $shouldExitWorkerLoop = false;
        while (! $shouldExitWorkerLoop) {
            /** @var ResponseCollection $exitReasons */
            $exitReasons = $this->eventManager->triggerEventUntil(
                function ($response) {
                    return $response instanceof ExitWorkerLoopResult;
                },
                new ProcessQueueEvent($this, $queue, $options)
            );

            if ($exitReasons->stopped() && $exitReasons->last()) {
                $shouldExitWorkerLoop = true;
            }
        }

        $this->eventManager->triggerEvent(new FinishEvent($this, $queue));

        $queueState = $this->eventManager->triggerEvent(new ProcessStateEvent($this));
        $queueState = array_filter(iterator_to_array($queueState));

        if ($exitReasons->last()) {
            $queueState[] = $exitReasons->last();
        }

        // cast to string
        $queueState = array_map('strval', $queueState);

        return $queueState;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
