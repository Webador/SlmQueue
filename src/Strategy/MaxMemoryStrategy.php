<?php

namespace SlmQueue\Strategy;

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
     * @param  WorkerEvent $event
     * @return void
     */
    public function onStopConditionCheck(WorkerEvent $event)
    {
        // @see http://php.net/manual/en/features.gc.collecting-cycles.php
        if (gc_enabled()) {
            gc_collect_cycles();
        }

        $usage = memory_get_usage();

        if ($this->maxMemory && $usage > $this->maxMemory) {
            $event->exitWorkerLoop();

            $this->state = sprintf(
                "memory threshold of %s exceeded (usage: %s)",
                $this->humanFormat($this->maxMemory),
                $this->humanFormat($usage)
            );

            return $event;
        } else {
            $this->state = sprintf('%s memory usage', $this->humanFormat($usage));
        }
    }

    /**
     * @param  string $bytes Bytes to be formatted
     * @return string human readable
     */
    private function humanFormat($bytes)
    {
        $units = ['b','kB','MB','GB','TB','PB'];
        return @round($bytes/pow(1024, ($i=floor(log($bytes, 1024)))), 2) . $units[$i];
    }
}
