<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class InterruptStrategy extends AbstractStrategy
{
    /**
     * @var bool
     */
    protected $interrupted = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        parent::__construct($options);

        // Conditional because of lack of pcntl_signal on windows
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'onPCNTLSignal']);
            pcntl_signal(SIGINT, [$this, 'onPCNTLSignal']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_IDLE,
            [$this, 'onStopConditionCheck'],
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_QUEUE,
            [$this, 'onStopConditionCheck'],
            -1000
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_STATE,
            [$this, 'onReportQueueState'],
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
        declare(ticks = 1);

        if ($this->interrupted) {
            $event->exitWorkerLoop();

            $this->state = sprintf("interrupt by an external signal on '%s'", $event->getName());
        }
    }

    /**
     * Handle the signal
     *
     * @param int $signo
     */
    public function onPCNTLSignal($signo)
    {
        switch ($signo) {
            case SIGTERM:
            case SIGINT:
                $this->interrupted = true;
                break;
        }
    }
}
