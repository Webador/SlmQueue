<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class MaxMemoryStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $maxMemory;

    /**
     * @param int $maxMemory
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = (int) $maxMemory;
    }

    /**
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_IDLE, array($this, 'onStopConditionCheck'), $priority);
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onStopConditionCheck'), $priority);
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        if ($this->maxMemory && memory_get_usage() > $this->maxMemory) {
            $event->stopPropagation();

            $this->exitState = sprintf("memory threshold of '%s' exceeded", memory_get_usage());
        } else {
            $this->exitState = sprintf('%s memory usage', memory_get_usage());
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
