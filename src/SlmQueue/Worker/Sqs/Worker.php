<?php

namespace SlmQueue\Worker\Sqs;

use SlmQueue\Job\JobInterface;
use SlmQueue\Queue\QueueInterface;
use SlmQueue\Worker\AbstractWorker;

/**
 * SqsWorker
 */
class Worker extends AbstractWorker
{
    /**
     * {@inheritDoc}
     */
    public function processJob(JobInterface $job, QueueInterface $queue)
    {
        // TODO: Implement processJob() method.
    }
}
