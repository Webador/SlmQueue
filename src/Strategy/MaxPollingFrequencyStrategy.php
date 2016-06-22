<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueue\Worker\Event\ProcessQueueEvent;
use Zend\EventManager\EventManagerInterface;

class MaxPollingFrequencyStrategy extends AbstractStrategy
{
    /**
     * @var int
     */
    protected $maxFrequency;

    /**
     * @var int
     */
    protected $lastTime = 0;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_QUEUE,
            [$this, 'onQueueProcessFinish'],
            1000
        );
    }

    /**
     * @param ProcessQueueEvent $event
     * @return void
     */
    public function onQueueProcessFinish(ProcessQueueEvent $event)
    {
        $startTime = microtime(true);
        $time      = ($startTime - $this->lastTime);

        $minTime = 1 / $this->maxFrequency;

        if ($time < $minTime) {
            $waitTime = $minTime - $time;
            usleep($waitTime * 1000000);
        }

        $this->lastTime = microtime(true);
    }

    /**
     * @param mixed $maxFrequency
     */
    public function setMaxFrequency($maxFrequency)
    {
        $this->maxFrequency = $maxFrequency;
    }

    /**
     * @return mixed
     */
    public function getMaxFrequency()
    {
        return $this->maxFrequency;
    }
}
