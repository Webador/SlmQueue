<?php

namespace SlmQueue\Worker;

use SlmQueue\Job\JobInterface;

/**
 * A worker is responsible to execute a job. It provides an easy way to wrap logic handling, error detection and
 * logging when executing a job. SlmQueue provides out of the box two workers: one for Beanstalk and one for Amazon SQS
 */
interface WorkerInterface
{
    /**
     * Execute the given job
     *
     * @param  JobInterface $job
     * @return mixed
     */
    public function execute(JobInterface $job);
}
