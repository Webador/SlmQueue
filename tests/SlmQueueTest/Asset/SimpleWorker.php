<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;

class SimpleWorker extends AbstractWorker
{
    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        return $job->execute();
    }
}
