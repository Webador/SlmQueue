<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class InterruptStrategy extends AbstractStrategy
{
    /**
     * @var bool
     */
    protected $interrupted = false;

    public function __construct()
    {
        if (function_exists('pcntl_signal')) { // Conditional because of lack of pcntl_signal on windows
            declare(ticks = 1);
            pcntl_signal(SIGTERM, array($this, 'onPCNTLSignal'));
            pcntl_signal(SIGINT, array($this, 'onPCNTLSignal'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_IDLE,
            array($this, 'onStopConditionCheck'),
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_JOB_POST,
            array($this, 'onStopConditionCheck'),
            $priority
        );
    }

    /**
     * Checks for the stop condition of this strategy
     *
     * @param WorkerEvent $event
     * @return string
     */
    public function onStopConditionCheck(WorkerEvent $event)
    {
        if ($this->interrupted) {
            $event->stopPropagation();

            $this->exitState = sprintf("interrupt by an external signal on '%s'", $event->getName());
        }
    }

    /**
     * Handle the signal
     *
     * @param int $signo
     */
    public function onPCNTLSignal($signo)
    {
        switch($signo) {
            case SIGTERM:
            case SIGINT:
                $this->interrupted = true;
                break;
        }
    }
}
