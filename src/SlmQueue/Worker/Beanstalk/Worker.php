<?php

namespace SlmQueue\Worker\Beanstalk;

use SlmQueue\Job\JobInterface;
use SlmQueue\Worker\WorkerInterface;

/**
 * Worker for Beanstalk
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
