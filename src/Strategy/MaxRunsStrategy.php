<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;
use Zend\EventManager\EventManagerInterface;

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

    /**
     * @param int $maxRuns
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = $maxRuns;
    }

    /**
     * @return int
     */
    public function getMaxRuns()
    {
        return $this->maxRuns;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AbstractWorkerEvent::EVENT_PROCESS_QUEUE,
            [$this, 'onStopConditionCheck'],
            -1000
        );
        $this->listeners[] = $events->attach(
            AbstractWorkerEvent::EVENT_PROCESS_STATE,
            [$this, 'onReportQueueState'],
            $priority
        );
    }

    /**
     * @param AbstractWorkerEvent $event
     * @return ExitWorkerLoopResult|void
     */
    public function onStopConditionCheck(AbstractWorkerEvent $event)
    {
        $this->runCount++;

        $this->state = sprintf('%s jobs processed', $this->runCount);

        if ($this->maxRuns && $this->runCount >= $this->maxRuns) {
            $reason = sprintf('maximum of %s jobs processed', $this->runCount);

            return ExitWorkerLoopResult::withReason($reason);
        }
    }
}
