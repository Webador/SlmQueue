<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Result\ExitWorkerLoopResult;

class MaxMemoryStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $maxMemory = 0;

    public function setMaxMemory(int $maxMemory): void
    {
        $this->maxMemory = $maxMemory;
    }

    public function getMaxMemory(): int
    {
        return $this->maxMemory;
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
        // @see http://php.net/manual/en/features.gc.collecting-cycles.php
        if (gc_enabled()) {
            gc_collect_cycles();
        }

        $usage = memory_get_usage();
        $this->state = sprintf('%s memory usage', $this->humanFormat($usage));

        if ($this->maxMemory && $usage > $this->maxMemory) {
            $reason = sprintf(
                "memory threshold of %s exceeded (usage: %s)",
                $this->humanFormat($this->maxMemory),
                $this->humanFormat($usage)
            );

            return ExitWorkerLoopResult::withReason($reason);
        }

        return null;
    }

    private function humanFormat(int $bytes): string
    {
        $units = ['b', 'kB', 'MB', 'GB', 'TB', 'PB'];

        return @round($bytes / (1024 ** ($i = floor(log($bytes, 1024)))), 2) . $units[$i];
    }
}
