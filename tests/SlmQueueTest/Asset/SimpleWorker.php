<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;
use Zend\EventManager\EventManager;

class SimpleWorker extends AbstractWorker
{
    public function __construct()
    {
        parent::__construct(new EventManager);
    }

    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        return $job->execute();
    }
}
