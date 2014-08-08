<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
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
            WorkerEvent::EVENT_PROCESS,
            array($this, 'onStopConditionCheck'),
            -1000
        );
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_STATE,
            array($this, 'onReportQueueState'),
            $priority
        );
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        $this->runCount++;

        if ($this->maxRuns && $this->runCount >= $this->maxRuns) {
            $event->exitWorkerLoop();

            $this->state = sprintf('maximum of %s jobs processed', $this->runCount);
        } else {
            $this->state = sprintf('%s jobs processed', $this->runCount);
        }
    }
}
