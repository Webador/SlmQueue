<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\Event\BootstrapEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use Zend\EventManager\EventManagerInterface;

class MaxTimeStrategy extends AbstractStrategy
{
    /**
     * The timestamp when the worker has started
     *
     * @var int
     */
    protected $startTime = PHP_INT_MAX;

    /**
     * The maximum amount of seconds the worker may do its work
     *
     * @var int
     */
    protected $maxTime = 3600;

    /**
     * {@inheritDoc}
     */
    protected $state = '0 seconds passed';

    /**
     * @param int $maxTime
     */
    public function setMaxTime(int $maxTime)
    {
        $this->maxTime = $maxTime;
    }

    /**
     * @return int
     */
    public function getMaxTime(): int
    {
        return $this->maxTime;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_BOOTSTRAP,
            [$this, 'onBootstrap'],
            $priority
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'checkRuntime'],
            -1000
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_IDLE,
            [$this, 'checkRuntime'],
            -1000
        );

        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_STATE,
            [$this, 'onReportQueueState'],
            $priority
        );
    }

    /**
     * @param BootstrapEvent $event
     */
    public function onBootstrap(BootstrapEvent $event)
    {
        $this->startTime = time();
    }

    /**
     * @param WorkerEventInterface $event
     *
     * @return ExitWorkerLoopResult|null
     */
    public function checkRuntime(WorkerEventInterface $event): ?ExitWorkerLoopResult
    {
        $now         = time();
        $diff        = $now - $this->startTime;
        $this->state = sprintf('%d seconds passed', $diff);

        if ($diff >= $this->maxTime) {
            $reason = sprintf('maximum of %d seconds passed', round($this->maxTime));

            return ExitWorkerLoopResult::withReason($reason);
        }
        return null;
    }
}
