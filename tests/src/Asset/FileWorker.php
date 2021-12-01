<?php

namespace SlmQueueTest\Asset;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;
use SlmQueue\Worker\Event\ProcessJobEvent;

class FileWorker extends AbstractWorker
{
    public function processJob(JobInterface $job, QueueInterface $queue): int
    {
        $job->execute();
        return ProcessJobEvent::JOB_STATUS_SUCCESS;
    }
}
