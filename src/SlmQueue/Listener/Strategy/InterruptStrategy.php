<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class InterruptStrategy extends AbstractStrategy {

    /**
     * @var bool
     */
    protected $interrupted = false;

    public function __construct()
    {
        // Listen to the signals SIGTERM and SIGINT so that the worker can be killed properly. Note that
        // because pcntl_signal may not be available on Windows, we needed to check for the existence of the function
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            pcntl_signal(SIGTERM, array($this, 'handleSignal'));
            pcntl_signal(SIGINT,  array($this, 'handleSignal'));
        }
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
        if ($this->interrupted) {
            $event->stopPropagation(true);

            return 'interrupted by an external signal';
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
                $this->interrupted = true;
                break;
        }
    }

}