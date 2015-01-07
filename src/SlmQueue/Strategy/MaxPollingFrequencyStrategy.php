<?php

namespace SlmQueue\Strategy;

use SlmQueue\Worker\WorkerEvent;
use Zend\EventManager\EventManagerInterface;
use SlmQueue\Strategy\AbstractStrategy;

class MaxPollingFrequencyStrategy extends AbstractStrategy
{
    protected $maxFrequency = 0;

    protected $lastTime = 0;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            WorkerEvent::EVENT_PROCESS_QUEUE,
            array($this, 'onQueueProcessFinish'),
            1000
        );
    }

    public function onQueueProcessFinish(WorkerEvent $event)
    {
        $startTime = microtime(true);
        $time = ($startTime - $this->lastTime);

        $minTime = 1 / $this->maxFrequency;

        if($time < $minTime) {
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
