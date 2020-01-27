<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

class SimpleWorker extends AbstractWorker
{
    public function __construct(EventManagerInterface $eventManager = null)
    {
        if (null === $eventManager) {
            $eventManager = new EventManager;
        }
        parent::__construct($eventManager);
    }

    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        return $job->execute();
    }
}
