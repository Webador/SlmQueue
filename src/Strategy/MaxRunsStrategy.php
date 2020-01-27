<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

class MaxRunsStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $runCount = 0;

    /**
     * @var int
     */
    protected $maxRuns = 0;

    /**
     * {@inheritDoc}
     */
    protected $state = '0 jobs processed';

    public function setMaxRuns(int $maxRuns): void
    {
        $this->maxRuns = $maxRuns;
    }

    public function getMaxRuns(): int
    {
        return $this->maxRuns;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
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
        $this->runCount++;

        $this->state = sprintf('%s jobs processed', $this->runCount);

        if ($this->maxRuns && $this->runCount >= $this->maxRuns) {
            $reason = sprintf('maximum of %s jobs processed', $this->runCount);

            return ExitWorkerLoopResult::withReason($reason);
        }

        return null;
    }
}
