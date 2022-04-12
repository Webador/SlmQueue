<?php

namespace SlmQueue\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;

class MaxPollingFrequencyStrategy extends AbstractStrategy
{
    /**
     * @var float
     */
    protected $maxFrequency;

    /**
     * @var float
     */
    protected $lastTime = 0.0;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'onQueueProcessFinish'],
            1000
        );
    }

    public function onQueueProcessFinish(ProcessQueueEvent $event): void
    {
        $startTime = microtime(true);
        $time = ($startTime - $this->lastTime);

        $minTime = 1.0 / $this->maxFrequency;

        if ($time < $minTime) {
            $waitTime = $minTime - $time;
            usleep(round($waitTime * 1000000));
        }

        $this->lastTime = microtime(true);
    }

    public function setMaxFrequency(float $maxFrequency): void
    {
        $this->maxFrequency = $maxFrequency;
    }

    public function getMaxFrequency(): float
    {
        return $this->maxFrequency;
    }
}
