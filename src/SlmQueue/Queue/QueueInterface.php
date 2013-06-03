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
     * @param  array        $options
     * @return void
     */
    public function push(JobInterface $job, array $options = array());

    /**
     * Pop a job (or multiple jobs) from the queue
     *
     * @param  array $options
     * @return JobInterface
     */
    public function pop(array $options = array());

    /**
     * Delete a job from the queue
     *
     * @param  JobInterface $job
     * @return void
     */
    public function delete(JobInterface $job);
}
