<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

class InterruptStrategy extends AbstractStrategy
{
    /**
     * @var bool
     */
    protected $interrupted = false;

    public function __construct(array $options = null)
    {
        parent::__construct($options);

        // Conditional because of lack of pcntl_signal on windows
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'onPCNTLSignal']);
            pcntl_signal(SIGINT, [$this, 'onPCNTLSignal']);
        }
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_IDLE,
            [$this, 'onStopConditionCheck'],
            $priority
        );
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'onStopConditionCheck'],
            -1000
        );
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_STATE,
            [$this, 'onReportQueueState'],
            $priority
        );
    }

    public function onStopConditionCheck(WorkerEventInterface $event): ?ExitWorkerLoopResult
    {
        declare(ticks=1);

        if ($this->interrupted) {
            $reason = sprintf("interrupt by an external signal on '%s'", $event->getName());

            return ExitWorkerLoopResult::withReason($reason);
        }

        return null;
    }

    public function onPCNTLSignal(int $signal): void
    {
        switch ($signal) {
            case SIGTERM:
            case SIGINT:
                $this->interrupted = true;
                break;
        }
    }
}
