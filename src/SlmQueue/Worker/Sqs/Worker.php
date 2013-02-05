<?php

namespace SlmQueue\Worker\Sqs;

use SlmQueue\Job\JobInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * Worker for Amazon SQS
 */
class Worker implements WorkerInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute(JobInterface $job)
    {
        // TODO: Implement execute() method.
    }
}
