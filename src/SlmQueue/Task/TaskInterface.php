<?php

namespace SlmQueue\Task;

use SlmQueue\Job\JobInterface;

/**
 * TaskInterface
 */
interface TaskInterface
{
    /**
     * Execute a single task of the job
     *
     * @param  JobInterface $job
     * @return void
     */
    public function execute(JobInterface $job);
}
