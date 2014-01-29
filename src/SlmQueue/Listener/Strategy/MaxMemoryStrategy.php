<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class MaxMemoryStrategy extends AbstractStrategy {

    /**
     * @var int
     */
    protected $max_memory;

    /**
     * @param int $max_memory
     */
    public function setMaxMemory($max_memory)
    {
        $this->max_memory = $max_memory;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->handlers[] = $events->attach(WorkerEvent::EVENT_PROCESS_IDLE, array($this, 'onStopConditionCheck'));
        $this->handlers[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onStopConditionCheck'));
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        if ($this->max_memory && memory_get_usage() > $this->max_memory) {
            $event->stopPropagation(true);

            return 'reached maximum allowed memory usage';
        }
    }

    /**
     * Handle the signal
     *
     * @param int $signo
     */
    public function handleSignal($signo)
    {
        switch($signo) {
            case SIGTERM:
            case SIGINT:
                $this->stopped = true;
                break;
        }
    }

}