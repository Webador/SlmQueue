<?php

namespace SlmQueue\Queue;

use SlmQueue\Job\JobInterface;

/**
 * Contract for a queue
 */
interface QueueInterface
{
    /**
     * Get the name of the queue
     *
     * @return string
     */
    public function getName();

    /**
     * Get the job plugin manager
     *
     * @return \SlmQueue\Job\JobPluginManager
     */
    public function getJobPluginManager();

    /**
     * Push a new job into the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function push(JobInterface $job);

    /**
     * Pop a job (or multiple jobs) from the queue
     *
     * @return JobInterface|array
     */
    public function pop();

    /**
     * Delete a job from the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function delete(JobInterface $job);
}
