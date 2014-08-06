<?php

namespace SlmQueue\Listener\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;

class MaxMemoryStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $maxMemory;

    /**
     * @param int $maxMemory
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = (int) $maxMemory;
    }

    /**
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
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
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_STATE,
            array($this, 'onReportQueueState'),
            $priority
        );
    }

    public function onStopConditionCheck(WorkerEvent $event)
    {
        if ($this->maxMemory && memory_get_usage() > $this->maxMemory) {
            $event->exitWorkerLoop();

            $this->state = sprintf(
                "memory threshold of %s exceeded (usage: %s)",
                $this->humanFormat($this->maxMemory),
                $this->humanFormat(memory_get_usage())
            );
        } else {
            $this->state = sprintf('%s memory usage', $this->humanFormat(memory_get_usage()));
        }
    }

    /**
     * @param $bytes bytes to be formatted
     * @return string human readable
     */
    private function humanFormat($bytes)
    {
        $units = array('b','kB','MB','GB','TB','PB');
        return @round($bytes/pow(1024, ($i=floor(log($bytes, 1024)))), 2) . $units[$i];
    }
}
