<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class MaxRunsStrategy extends AbstractStrategy {

    protected $run_count = 0;

    /**
     * @var int
     */
    protected $max_runs = 0;

    /**
     * @param int $max_runs
     */
    public function setMaxRuns($max_runs)
    {
        $this->max_runs = $max_runs;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->handlers[] = $events->attach(WorkerEvent::EVENT_PROCESS_JOB_POST, array($this, 'onStopConditionCheck'));
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        $this->run_count++;

        if ($this->max_runs && $this->run_count >= $this->max_runs) {
            $event->stopPropagation(true);

            return 'reached its maximum allowed runs';
        }
    }

}