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
            WorkerEvent::EVENT_PROCESS_JOB_POST,
            array($this, 'onStopConditionCheck'),
            $priority
        );

        $this->exitState = sprintf('%s jobs processed', $this->runCount);
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        $this->runCount++;

        if ($this->maxRuns && $this->runCount >= $this->maxRuns) {
            $event->stopPropagation();

            $this->exitState = sprintf('maximum of %s jobs processed', $this->runCount);
        } else {
            $this->exitState = sprintf('%s jobs processed', $this->runCount);
        }
    }
}
