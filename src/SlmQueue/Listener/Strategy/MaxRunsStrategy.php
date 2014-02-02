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
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onStopConditionCheck'));
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        $this->runCount++;

        if ($this->maxRuns && $this->runCount >= $this->maxRuns) {
            $event->stopPropagation(true);

            return 'reached its maximum allowed runs';
        }
    }

}
